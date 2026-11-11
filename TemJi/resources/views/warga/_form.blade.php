<div class="mb-3">
    <label class="form-label">Warga</label>
    <input class="form-control" value="{{ $warga->nama_lengkap }} ({{ $warga->no_kk }})" disabled>
</div>
<div class="mb-3">
    <label class="form-label">Wilayah</label>
    <select name="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror">
        <option value="">— pilih wilayah —</option>
        @foreach ($wilayahs as $w)
            <option value="{{ $w->id }}" @selected((string) old('wilayah_id', $warga->wilayah_id) === (string) $w->id)>{{ $w->nama }}</option>
        @endforeach
    </select>
    @error('wilayah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.warga.index') }}" class="btn btn-secondary">Batal</a>
