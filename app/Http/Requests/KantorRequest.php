<?php

namespace App\Http\Requests;

class KantorRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('kantor');

        return [
            'nama' => 'required|string|max:100',
            'kode' => "nullable|string|max:20|unique:kantors,kode,{$id}",
            'alamat' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'nullable|integer|min:10|max:5000',
            'is_aktif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kantor wajib diisi.',
            'latitude.required' => 'Koordinat latitude wajib diisi.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.required' => 'Koordinat longitude wajib diisi.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'radius_meter.min' => 'Radius minimal 10 meter.',
            'radius_meter.max' => 'Radius maksimal 5000 meter.',
            'kode.unique' => 'Kode kantor sudah digunakan.',
        ];
    }
}
