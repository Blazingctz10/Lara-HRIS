@php
    $cvPath = $record->cv_path;
    $cvUrl = $cvPath ? asset('storage/' . $cvPath) : null;
    $isImage = false;
    $isPdf = false;
    if ($cvPath) {
        $ext = strtolower(pathinfo($cvPath, PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
        $isPdf = ($ext === 'pdf');
    }
@endphp

<div style="width: 100%; box-sizing: border-box;">
    @if ($cvPath)
        @if ($isPdf)
            <!-- Pratinjau Murni CV Format PDF -->
            <div style="border-radius: 16px; overflow: hidden; border: 1px solid rgba(0, 0, 0, 0.1); width: 100%; height: 75vh; box-sizing: border-box;">
                <iframe src="{{ $cvUrl }}#toolbar=1" style="width: 100%; height: 100%; border: none; display: block;" title="Pratinjau CV PDF"></iframe>
            </div>
        @elseif ($isImage)
            <!-- Pratinjau Murni CV Format Gambar -->
            <div style="display: flex; justify-content: center; align-items: center; background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 16px; padding: 12px; width: 100%; box-sizing: border-box;">
                <img src="{{ $cvUrl }}" alt="Berkas CV Pelamar" 
                     style="max-width: 100%; max-height: 75vh; width: auto; height: auto; object-fit: contain; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12); display: block;" />
            </div>
        @else
            <!-- Berkas Lain (DOC/DOCX) -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; border-radius: 12px; background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.08);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 44px; height: 44px; background: rgba(79, 70, 229, 0.1); color: #4F46E5; font-weight: bold; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                        DOC
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #111;">{{ $record->cv_filename ?: basename($cvPath) }}</div>
                        <div style="font-size: 12px; color: #666;">Berkas CV Digital Pelamar</div>
                    </div>
                </div>
                <a href="{{ $cvUrl }}" download style="padding: 8px 16px; border-radius: 8px; background: #4F46E5; color: #fff; font-size: 12px; font-weight: 600; text-decoration: none;">Unduh CV</a>
            </div>
        @endif
    @else
        <!-- Keadaan Kosong Tanpa CV -->
        <div style="padding: 32px 20px; text-align: center; color: #666; font-size: 14px; background: rgba(0, 0, 0, 0.02); border-radius: 14px; border: 1px dashed rgba(0, 0, 0, 0.15);">
            <div style="font-size: 24px; margin-bottom: 8px;">📑</div>
            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">Tidak Ada Berkas CV</div>
            <div style="font-size: 12px; color: #888;">Pelamar ini belum melampirkan berkas Curriculum Vitae (CV).</div>
        </div>
    @endif
</div>
