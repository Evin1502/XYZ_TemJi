<div class="mb-3">
    <label class="form-label">Nomor KK</label>
    <input type="text" name="no_kk" maxlength="16" inputmode="numeric"
           class="form-control @error('no_kk') is-invalid @enderror"
           value="{{ old('no_kk', $warga->no_kk ?? '') }}">
    @error('no_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Nama kepala keluarga</label>
    <input type="text" name="nama_kepala_keluarga"
           class="form-control @error('nama_kepala_keluarga') is-invalid @enderror"
           value="{{ old('nama_kepala_keluarga', $warga->nama_kepala_keluarga ?? '') }}">
    @error('nama_kepala_keluarga') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Alamat</label>
    <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $warga->alamat ?? '') }}</textarea>
    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Wilayah</label>
    <select name="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror">
        <option value="">-- Pilih wilayah --</option>
        @foreach ($wilayahs as $w)
            <option value="{{ $w->id }}" {{ (string) old('wilayah_id', $warga->wilayah_id ?? '') === (string) $w->id ? 'selected' : '' }}>
                {{ $w->nama }}
            </option>
        @endforeach
    </select>
    @error('wilayah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('warga.index') }}" class="btn btn-secondary">Batal</a>
