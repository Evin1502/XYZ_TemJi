<div class="mb-3">
    <label class="form-label">Nama wilayah</label>
    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
           value="{{ old('nama', $wilayah->nama ?? '') }}">
    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Jumlah KK</label>
    <input type="number" name="jumlah_kk" min="0" class="form-control @error('jumlah_kk') is-invalid @enderror"
           value="{{ old('jumlah_kk', $wilayah->jumlah_kk ?? 0) }}">
    @error('jumlah_kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="form-check mb-3">
    <input type="hidden" name="is_aktif" value="0">
    <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="is_aktif"
           {{ old('is_aktif', $wilayah->is_aktif ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_aktif">Aktif</label>
</div>
<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('wilayah.index') }}" class="btn btn-secondary">Batal</a>