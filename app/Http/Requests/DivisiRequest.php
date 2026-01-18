<?php

namespace App\Http\Requests;

class DivisiRequest extends BaseRequest
{
    public function rules(): array
    {
        $id = $this->route('divisi');

        return [
            'kode' => "nullable|string|max:20|unique:divisis,kode,{$id}",
            'nama' => 'required|string|max:100',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
            'toleransi_terlambat' => 'nullable|integer|min:0|max:120',
            'is_aktif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama divisi wajib diisi.',
            'jam_masuk.required' => 'Jam masuk wajib diisi.',
            'jam_pulang.required' => 'Jam pulang wajib diisi.',
            'jam_pulang.after' => 'Jam pulang harus setelah jam masuk.',
            'kode.unique' => 'Kode divisi sudah digunakan.',
        ];
    }
}
