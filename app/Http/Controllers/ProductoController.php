<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,vendedor']);

        $this->middleware(function ($request, $next) {
            if (auth()->user()->rol === 'pendiente') {
                abort(403, 'Cuenta pendiente de aprobación.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar todos los productos
     */
    public function index(Request $request)
    {
        $query = Producto::with('imagenes');

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('precio', 'like', "%{$search}%")
                  ->orWhere('cantidad', 'like', "%{$search}%");
            });
        }

        $productos = $query->orderBy('id', 'desc')->paginate(9);

        // Si es AJAX, solo devolvemos las tarjetas
        if ($request->ajax()) {
            return view('productos.partials.productos_grid', compact('productos'))->render();
        }

        return view('productos.index', compact('productos'));
    }

    public function searchPreview(Request $request)
    {
        $query = $request->get('q', '');

        $productos = Producto::query()
            ->where('id', 'like', "%{$query}%")
            ->orWhere('nombre', 'like', "%{$query}%")
            ->orWhere('descripcion', 'like', "%{$query}%")
            ->orWhere('precio', 'like', "%{$query}%")
            ->orWhere('cantidad', 'like', "%{$query}%")
            ->orWhere('creado_por', 'like', "%{$query}%")
            ->orWhere('categoria_id', 'like', "%{$query}%")
            ->orWhere('proveedor_id', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        return view('productos.partials.search_preview', compact('productos'));
    }


    public function search(Request $request)
    {
        $query = $request->input('q');
        $user = auth()->user();

        $productos = Producto::query()
            ->when($query, function ($q) use ($query, $user) {
                $q->where(function ($sub) use ($query, $user) {
                    $sub->where('id', 'like', "%{$query}%")
                        ->orWhere('nombre', 'like', "%{$query}%")
                        ->orWhere('descripcion', 'like', "%{$query}%")
                        ->orWhere('precio', 'like', "%{$query}%");

                    if ($user && $user->rol === 'admin') {
                        $sub->orWhereHas('proveedor', function ($proveedorQuery) use ($query) {
                            $proveedorQuery->where('nombre', 'like', "%{$query}%");
                        });
                    }
                });
            })
            ->distinct()
            ->orderByRaw("
                CASE
                    WHEN id LIKE ? THEN 1
                    WHEN nombre LIKE ? THEN 2
                    WHEN descripcion LIKE ? THEN 3
                    WHEN precio LIKE ? THEN 4
                    ELSE 5
                END
            ", ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"])
            ->paginate(20);

        return view('productos.search_results', compact('productos', 'query'));
    }





    /**
     * Formulario de creación
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('productos.create', compact('categorias', 'proveedores'));
    }

    /**
     * Guardar un nuevo producto
     */
    public function store(Request $request)
    {
        // Calculate total file size safely
        $totalFileSize = 0;
        if (isset($_FILES)) {
            foreach ($_FILES as $fileGroup) {
                if (is_array($fileGroup['size'])) {
                    $totalFileSize += array_sum($fileGroup['size']);
                } else {
                    $totalFileSize += $fileGroup['size'];
                }
            }
        }

        \Log::info('[ProductoController@store] Upload debug start', [
            'has_galeria' => $request->hasFile('galeria'),
            'galeria_count' => $request->hasFile('galeria') ? count($request->file('galeria')) : 0,
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'request_files' => $request->allFiles(),
            'total_file_size' => $totalFileSize,
            'post_size_estimate' => strlen(serialize($_POST)) + $totalFileSize,
        ]);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            // precio must be integer (no decimals) and limited in size
            'precio' => ['required','integer','min:0','max:99999999'],
            'cantidad' => 'required|integer',
            'categoria_id' => 'nullable|exists:categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            // galeria[] permite múltiples imágenes/videos (cada uno hasta 50MB)
            'galeria.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4|max:51200',
            // id (índice) del archivo de galeria que será principal
            'galeria_principal' => 'nullable|integer',
        ]);

        $data = $request->only('nombre', 'descripcion', 'precio', 'cantidad', 'categoria_id', 'proveedor_id');
        $data['creado_por'] = auth()->id();

        // Ensure precio is stored as integer (no decimals)
        if (isset($data['precio'])) {
            $data['precio'] = (int) $data['precio'];
        }

        try {
            $producto = Producto::create($data);
            \Log::info('[ProductoController@store] Producto creado', ['producto_id' => $producto->id]);
        } catch (QueryException $e) {
            \Log::error('[ProductoController@store] DB error inserting producto', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return back()
                ->withInput()
                ->withErrors(['precio' => 'El precio debe ser un número entero sin decimales y no puede ser tan grande.']);
        }

        // Galería y selección de principal (si no se elige, será la primera)
        if ($request->hasFile('galeria')) {
            $files = array_values($request->file('galeria'));
            $principalIndex = $request->input('galeria_principal');
            
            \Log::info('[ProductoController@store] Processing galeria files', [
                'files_count' => count($files),
                'principal_index' => $principalIndex,
            ]);

            // Reordenar: principal primero si viene marcado
            if ($principalIndex !== null && isset($files[(int)$principalIndex])) {
                $principalFile = $files[(int)$principalIndex];
                unset($files[(int)$principalIndex]);
                array_unshift($files, $principalFile);
            }

            $seq = 1; // nuevo producto empieza en 1
            foreach ($files as $idx => $file) {
                \Log::info('[ProductoController@store] Processing file', [
                    'file_index' => $idx,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
                
                $ext = strtolower($file->getClientOriginalExtension() ?: '');
                if ($ext === '') {
                    // fallback simple por mime
                    $mime = $file->getMimeType();
                    $ext = $mime === 'video/mp4' ? 'mp4' : 'jpg';
                }
                $filename = $producto->id . '_' . $seq . '.' . $ext;
                $ruta = 'productos/' . $filename;
                // Normalizar rutas por si viene algún prefijo accidental (ej. "public/storage/...")
                $ruta = $this->normalizeRuta($ruta);
                // Guardar con nombre determinístico
                try {
                    $result = $file->storeAs('productos', $filename, 'public');
                    \Log::info('[ProductoController@store] File stored successfully', [
                        'filename' => $filename,
                        'stored_path' => $result,
                        'full_path' => storage_path('app/public/' . $ruta),
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('[ProductoController@store] Error al guardar archivo', [
                        'file' => $filename,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    continue; // saltar este archivo para no romper toda la transacción
                }

                // El primer archivo es la imagen principal: NO se guarda en producto_imagenes
                if ($idx === 0) {
                    $producto->imagen_principal = $ruta;
                    \Log::info('[ProductoController@store] Set principal image', ['ruta' => $ruta]);
                } else {
                    // El resto va a la galería
                    $productoImagen = ProductoImagen::create([
                        'producto_id' => $producto->id,
                        'ruta' => $ruta,
                        'es_principal' => false,
                    ]);
                    \Log::info('[ProductoController@store] Created gallery image', [
                        'imagen_id' => $productoImagen->id,
                        'ruta' => $ruta,
                    ]);
                }
                $seq++;
            }

            $producto->save();
            \Log::info('[ProductoController@store] Product saved with images', [
                'producto_id' => $producto->id,
                'imagen_principal' => $producto->imagen_principal,
            ]);
        } else {
            \Log::warning('[ProductoController@store] No se recibieron archivos en galeria');
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    /**
     * Mostrar un producto
     */
    public function show(Producto $producto)
    {
        $producto->load('imagenes');
        return view('productos.show', compact('producto'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Producto $producto)
    {
        $producto->load('imagenes');
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias', 'proveedores'));
    }

    /**
     * Actualizar un producto
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            // enforce integer on update as well
            'precio' => ['required','integer','min:0','max:99999999'],
            'cantidad' => 'required|integer',
            'categoria_id' => 'nullable|exists:categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'galeria.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4|max:51200',
            'galeria_principal' => 'nullable|integer',
        ]);

        $data = $request->only('nombre', 'descripcion', 'precio', 'cantidad', 'categoria_id', 'proveedor_id');

        // Force integer precio
        if (isset($data['precio'])) {
            $data['precio'] = (int) $data['precio'];
        }

        $producto->update($data);

        // Si se quiere cambiar cuál de la galería es principal: mover esa imagen a principal
        if ($request->filled('galeria_principal')) {
            $principalId = $request->input('galeria_principal');
            if (is_numeric($principalId)) {
                $img = ProductoImagen::where('producto_id', $producto->id)
                    ->where('id', $principalId)
                    ->first();
                if ($img) {
                    // Si ya existía una imagen principal, intercambiamos los nombres de archivo
                    // para que la nueva imagen principal ocupe el nombre de la anterior
                    // y la anterior pase a la galería sin eliminarse.
                    if ($producto->imagen_principal) {
                        $oldRuta = $producto->imagen_principal; // e.g. productos/{id}_1.jpg
                        $newRuta = $img->ruta; // e.g. productos/{id}_3.jpg

                        try {
                            // Generar ruta temporal para evitar colisiones
                            $tempRuta = 'productos/.swap_' . time() . '_' . uniqid() . '.tmp';

                            // Mover archivos: old -> temp, new -> old, temp -> new
                            if (Storage::disk('public')->exists($oldRuta)) {
                                Storage::disk('public')->move($oldRuta, $tempRuta);
                            } else {
                                // Si por alguna razón no existe el anterior, simplemente move new -> oldRuta
                                // (pero seguimos creando temp placeholder to keep logic simple)
                                // no-op for old
                            }

                            if (Storage::disk('public')->exists($newRuta)) {
                                Storage::disk('public')->move($newRuta, $oldRuta);
                            } else {
                                \Log::warning('[ProductoController@update] Nueva ruta de galería no existe en disco', ['ruta' => $newRuta]);
                            }

                            if (Storage::disk('public')->exists($tempRuta)) {
                                Storage::disk('public')->move($tempRuta, $newRuta);
                            }

                            // Después del intercambio de archivos, mantenemos el nombre de la imagen principal
                            // apuntando al mismo path ($oldRuta) porque ahora contiene la nueva imagen.
                            // No borramos ni eliminamos el registro de la galería; el registro sigue apuntando a $newRuta
                            // que ahora contiene la antigua imagen principal.
                            $producto->imagen_principal = $oldRuta;
                            $producto->save();

                        } catch (\Exception $e) {
                            \Log::error('[ProductoController@update] Error al intercambiar archivos de galería/principal', [
                                'producto_id' => $producto->id,
                                'oldRuta' => $oldRuta,
                                'newRuta' => $newRuta,
                                'error' => $e->getMessage(),
                            ]);
                            // No abortamos la petición completa; al menos informamos en logs.
                        }
                    } else {
                        // No había imagen principal: promovemos la imagen seleccionada a principal
                        $producto->imagen_principal = $img->ruta;
                        $producto->save();
                        // Eliminamos el registro de galería porque ahora es la principal
                        $img->delete(); // no borramos el archivo físico
                    }
                }
            }
        }

        // Agregar nuevos archivos a la galería con nombre consecutivo
        if ($request->hasFile('galeria')) {
            // Calcular siguiente secuencia revisando rutas existentes
            $producto->load('imagenes');
            $maxSeq = 0;
            $extractSeq = function($ruta) {
                // espera formato productos/{id}_{n}.ext
                $base = basename($ruta);
                $parts = explode('.', $base); // [id_n, ext]
                $name = $parts[0] ?? '';
                $underParts = explode('_', $name); // [id, n]
                $n = isset($underParts[1]) ? (int)$underParts[1] : 0;
                return $n;
            };
            if ($producto->imagen_principal) {
                $maxSeq = max($maxSeq, $extractSeq($producto->imagen_principal));
            }
            foreach ($producto->imagenes as $img) {
                $maxSeq = max($maxSeq, $extractSeq($img->ruta));
            }

            $seq = max(1, $maxSeq + 1);
            foreach ($request->file('galeria') as $file) {
                $ext = strtolower($file->getClientOriginalExtension() ?: '');
                if ($ext === '') {
                    $mime = $file->getMimeType();
                    $ext = $mime === 'video/mp4' ? 'mp4' : 'jpg';
                }
                $filename = $producto->id . '_' . $seq . '.' . $ext;
                $ruta = 'productos/' . $filename;
                // Normalizar rutas por si viene algún prefijo accidental
                $ruta = $this->normalizeRuta($ruta);
                $file->storeAs('productos', $filename, 'public');

                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta' => $ruta,
                    'es_principal' => false,
                ]);
                $seq++;
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar un producto
     */
    public function destroy(Producto $producto)
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403);
        }

        // Imagen principal
        if ($producto->imagen_principal && Storage::disk('public')->exists($producto->imagen_principal)) {
            Storage::disk('public')->delete($producto->imagen_principal);
        }

        // Galería
        foreach ($producto->imagenes as $img) {
            if (Storage::disk('public')->exists($img->ruta)) {
                Storage::disk('public')->delete($img->ruta);
            }
            $img->delete();
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Eliminar una imagen individual de la galería
     */
    public function deleteImage(ProductoImagen $imagen)
    {
        if (auth()->user()->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        try {
            // Detectar si la imagen es la imagen_principal actual del producto
            $producto = Producto::find($imagen->producto_id);
            $wasPrincipal = false;
            if ($producto && $producto->imagen_principal && $producto->imagen_principal === $imagen->ruta) {
                $wasPrincipal = true;
                // Limpiar la ruta principal en el producto
                $producto->imagen_principal = null;
                $producto->save();
            }

            // Eliminar archivo físico si existe
            if (Storage::disk('public')->exists($imagen->ruta)) {
                Storage::disk('public')->delete($imagen->ruta);
            }

            // Eliminar registro de la base de datos
            $imagenId = $imagen->id;
            $imagenRuta = $imagen->ruta;
            $imagen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada correctamente',
                'imagen_id' => $imagenId,
                'ruta' => $imagenRuta,
                'removed_principal' => $wasPrincipal,
            ]);
        } catch (\Exception $e) {
            \Log::error('[ProductoController@deleteImage] Error al eliminar imagen', [
                'imagen_id' => $imagen->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json(['success' => false, 'message' => 'Error al eliminar la imagen'], 500);
        }
    }

    /**
     * Normaliza una ruta antes de guardarla en la BD.
     * Quita prefijos accidentales como "public/storage/", "public/" o "storage/".
     */
    private function normalizeRuta(string $ruta): string
    {
        $ruta = preg_replace('#^(public\/storage\/|public\/|storage\/)#i', '', $ruta);
        return ltrim($ruta, '/');
    }
}