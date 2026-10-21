@php
    $receipt = $record->receipt_photo;
    $receiptUrl = $receipt ? asset('storage/' . $receipt) : null;
    $isImage = false;
    if ($receipt) {
        $ext = strtolower(pathinfo($receipt, PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'jfif', 'bmp']);
    }
@endphp

<div style="width: 100%; box-sizing: border-box;">
    @if ($receipt)
        @if ($isImage)
            <!-- Pratinjau Murni Foto Nota / Struk -->
            <div style="display: flex; justify-content: center; align-items: center; background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 16px; padding: 12px; width: 100%; box-sizing: border-box;">
                <img src="{{ $receiptUrl }}" alt="Bukti Struk Reimbursement" 
                     style="max-width: 100%; max-height: 75vh; width: auto; height: auto; object-fit: contain; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12); display: block;" />
            </div>
        @elseif(str_ends_with(strtolower($receipt), '.pdf'))
            <!-- Pratinjau Murni Struk Format PDF -->
            <div style="border-radius: 16px; overflow: hidden; border: 1px solid rgba(0, 0, 0, 0.1); width: 100%; height: 75vh; box-sizing: border-box;">
                <iframe src="{{ $receiptUrl }}#toolbar=1" style="width: 100%; height: 100%; border: none; display: block;" title="Dokumen Struk PDF"></iframe>
            </div>
        @else
            <!-- Berkas Lain (DOC/DOCX) -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; border-radius: 12px; background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.08);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 44px; height: 44px; background: rgba(14, 104, 56, 0.1); color: #0E6838; font-weight: bold; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                        DOC
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #111;">{{ basename($receipt) }}</div>
                        <div style="font-size: 12px; color: #666;">Berkas Dokumen Tagihan</div>
                    </div>
                </div>
                <a href="{{ $receiptUrl }}" download style="padding: 8px 16px; border-radius: 8px; background: #0E6838; color: #fff; font-size: 12px; font-weight: 600; text-decoration: none;">Unduh File</a>
            </div>
        @endif
    @else
        <!-- Keadaan Kosong Tanpa Struk -->
        <div style="padding: 32px 20px; text-align: center; color: #666; font-size: 14px; background: rgba(0, 0, 0, 0.02); border-radius: 14px; border: 1px dashed rgba(0, 0, 0, 0.15);">
            <div style="font-size: 24px; margin-bottom: 8px;">🧾</div>
            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">Tidak Ada Foto Bukti Struk</div>
            <div style="font-size: 12px; color: #888;">Klaim pengeluaran ini belum dilampirkan foto nota/struk fisik.</div>
        </div>
    @endif
</div>
