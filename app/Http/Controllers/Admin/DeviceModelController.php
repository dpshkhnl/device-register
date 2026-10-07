<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\DeviceModel;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceModelController extends Controller
{
    public function index(Request $request)
    {
        $deviceModels = DeviceModel::with(['product', 'brand'])
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->input('product_id')))
            ->when($request->filled('brand_id'), fn ($q) => $q->where('brand_id', $request->input('brand_id')))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%'))
            ->orderBy('product_id')
            ->orderBy('brand_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('admin.device-models.index', array_merge(
            compact('deviceModels'),
            $this->options(),
        ));
    }

    public function create()
    {
        return view('admin.device-models.create', $this->options());
    }

    public function store(Request $request)
    {
        DeviceModel::create($this->validated($request));

        return redirect()->route('admin.device-models.index')->with('status', 'Model created.');
    }

    public function edit(DeviceModel $deviceModel)
    {
        return view('admin.device-models.edit', array_merge(
            compact('deviceModel'),
            $this->options(),
        ));
    }

    public function update(Request $request, DeviceModel $deviceModel)
    {
        $deviceModel->update($this->validated($request, $deviceModel));

        return redirect()->route('admin.device-models.index')->with('status', 'Model updated.');
    }

    public function destroy(DeviceModel $deviceModel)
    {
        $deviceModel->delete();

        return redirect()->route('admin.device-models.index')->with('status', 'Model deleted.');
    }

    protected function options(): array
    {
        return [
            'products' => Product::orderBy('sort_order')->orderBy('name')->get(),
            'brands' => Brand::orderBy('sort_order')->orderBy('name')->get(),
        ];
    }

    protected function validated(Request $request, ?DeviceModel $deviceModel = null): array
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('device_models')
                    ->where('product_id', $request->input('product_id'))
                    ->where('brand_id', $request->input('brand_id'))
                    ->ignore($deviceModel),
            ],
            'storage_options' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Comma-separated input, e.g. "128, 256GB, 1tb" → ["128GB", "256GB", "1TB"]
        $storage = collect(explode(',', $data['storage_options'] ?? ''))
            ->map(fn ($value) => strtoupper(str_replace(' ', '', $value)))
            ->filter()
            ->map(fn ($value) => preg_match('/^\d+$/', $value) ? $value.'GB' : $value)
            ->unique()
            ->values()
            ->all();

        $data['name'] = trim($data['name']);
        $data['storage_options'] = $storage ?: null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
