<x-filament-panels::page>
    @php
        $employee = auth()->user()->employee;
        $branch = $employee?->branch;
    @endphp

    <style>
        .att-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .att-grid {
                grid-template-columns: 360px 1fr;
            }
        }
        .att-camera-box {
            position: relative;
            background: #090d16;
            border-radius: 0.75rem;
            overflow: hidden;
            width: 100%;
            height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed rgba(255, 255, 255, 0.15);
        }
        .att-camera-box video, .att-camera-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .att-watermark {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-family: monospace;
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .att-stat-card {
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        .dark .att-stat-card {
            border-color: rgba(255, 255, 255, 0.08);
        }
        .att-stat-in {
            background: rgba(16, 185, 129, 0.08);
            border-color: rgba(16, 185, 129, 0.25);
        }
        .att-stat-out {
            background: rgba(59, 130, 246, 0.08);
            border-color: rgba(59, 130, 246, 0.25);
        }
        .att-radar-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            position: relative;
        }
        .att-radar-pulse {
            position: absolute;
            top: -3px;
            left: -3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            opacity: 0.6;
            animation: radar-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes radar-ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        .att-geo-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .att-geo-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #059669;
        }
        .dark .att-geo-success {
            color: #34d399;
        }
        .att-geo-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }
        .dark .att-geo-danger {
            color: #f87171;
        }
    </style>

    <div class="att-grid" x-data="attendanceTracker()">
        <!-- Kolom Kiri: Informasi Penempatan & Status GPS -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Unit Kerja Penempatan -->
            <x-filament::section icon="heroicon-o-building-office-2">
                <x-slot name="heading">
                    Kantor Penempatan
                </x-slot>
                <x-slot name="description">
                    {{ $branch?->name ?? 'Belum Ditentukan' }}
                </x-slot>

                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.8rem; margin-top: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.4rem; border-bottom: 1px dashed rgba(128,128,128,0.2);">
                        <span style="color: #94a3b8;">Kode Cabang:</span>
                        <span style="font-weight: 700; font-family: monospace;">{{ $branch?->code ?? '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.4rem; border-bottom: 1px dashed rgba(128,128,128,0.2);">
                        <span style="color: #94a3b8;">Radius Geofence:</span>
                        <span style="font-weight: 700; color: #10b981;">{{ $branch?->radius_meters ?? 50 }} meter</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">Waktu Server:</span>
                        <span style="font-weight: 700; font-family: monospace; color: #38bdf8;" x-text="currentLiveTime">{{ now()->format('d M Y - H:i:s') }} WIB</span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Status Presensi Hari Ini -->
            <x-filament::section icon="heroicon-o-clock">
                <x-slot name="heading">
                    Presensi Hari Ini
                </x-slot>

                @if($todayAttendance)
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 0.5rem;">
                        <div class="att-stat-card att-stat-in">
                            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #059669;">Jam Masuk</div>
                            <div style="font-size: 1.3rem; font-weight: 800; font-family: monospace; color: #047857; margin: 0.25rem 0;">
                                {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }}
                            </div>
                            <x-filament::badge :color="$todayAttendance->status === 'Present' ? 'success' : 'warning'" size="sm">
                                {{ $todayAttendance->status === 'Present' ? 'Tepat Waktu' : 'Terlambat' }}
                            </x-filament::badge>
                        </div>

                        <div class="att-stat-card att-stat-out">
                            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #2563eb;">Jam Pulang</div>
                            <div style="font-size: 1.3rem; font-weight: 800; font-family: monospace; color: #1d4ed8; margin: 0.25rem 0;">
                                {{ $todayAttendance->clock_out ? \Carbon\Carbon::parse($todayAttendance->clock_out)->format('H:i') : '--:--' }}
                            </div>
                            <x-filament::badge :color="$todayAttendance->clock_out ? 'info' : 'gray'" size="sm">
                                {{ $todayAttendance->clock_out ? 'Sudah Pulang' : 'Belum Pulang' }}
                            </x-filament::badge>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 1.5rem 0.5rem; color: #94a3b8;">
                        <x-heroicon-o-x-circle style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem; opacity: 0.4;" />
                        <div style="font-size: 0.8rem; font-weight: 600;">Belum ada catatan presensi hari ini.</div>
                    </div>
                @endif
            </x-filament::section>

            <!-- Deteksi Lokasi GPS Geofence -->
            <x-filament::section icon="heroicon-o-map-pin">
                <x-slot name="heading">
                    Lokasi GPS & Radar Jarak
                </x-slot>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem;">
                    <div class="att-geo-banner" :class="isWithin ? 'att-geo-success' : 'att-geo-danger'">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span class="att-radar-dot" :style="isWithin ? 'background: #10b981;' : 'background: #ef4444;'">
                                <span class="att-radar-pulse" :style="isWithin ? 'background: #10b981;' : 'background: #ef4444;'"></span>
                            </span>
                            <span x-text="locationStatus">Mendeteksi lokasi...</span>
                        </div>
                        <span style="font-family: monospace; font-size: 0.9rem;" x-text="distanceText">-- m</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <p style="font-size: 0.725rem; color: #94a3b8; line-height: 1.4; margin: 0;" x-show="!isWithin">
                            ⚠️ <strong>Radius < {{ $branch?->radius_meters ?? 50 }}m</strong> kantor cabang.
                        </p>
                        <button type="button" @click="simulateAtOffice()" style="font-size: 0.725rem; color: #6366f1; text-decoration: underline; background: none; border: none; cursor: pointer;">
                            🎯 Simulasi di Lokasi Kantor
                        </button>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Kolom Kanan: Kamera Selfie & Kontrol Tombol -->
        <div>
            <x-filament::section>
                <x-slot name="heading">
                    Kamera Selfie Verifikasi Kehadiran
                </x-slot>
                <x-slot name="description">
                    Ambil foto selfie di area kantor untuk validasi kehadiran
                </x-slot>
                <x-slot name="headerEnd">
                    <x-filament::button size="xs" color="gray" icon="heroicon-m-arrow-path" @click="startCamera()">
                        Nyalakan Kamera
                    </x-filament::button>
                </x-slot>

                <!-- Video / Preview Viewport -->
                <div class="att-camera-box" style="margin-top: 0.75rem;">
                    <video x-ref="videoElement" autoplay playsinline muted x-show="isCameraOn && !capturedPhoto"></video>
                    <canvas x-ref="canvasElement" style="display: none;"></canvas>
                    
                    <template x-if="capturedPhoto">
                        <img :src="capturedPhoto" alt="Captured Selfie" />
                    </template>

                    <div x-show="!isCameraOn && !capturedPhoto" style="text-align: center; color: #64748b; padding: 2rem;">
                        <x-heroicon-o-camera style="width: 3.5rem; height: 3.5rem; margin: 0 auto 0.75rem; opacity: 0.5;" />
                        <div style="font-size: 0.9rem; font-weight: 700; color: #e2e8f0;" x-text="cameraStatusTitle">Kamera Belum Aktif</div>
                        <div style="font-size: 0.775rem; color: #94a3b8; margin-top: 0.4rem; max-width: 360px; line-height: 1.5;" x-text="cameraStatusDesc">
                            Klik tombol "Nyalakan Kamera" atau izinkan browser mengakses kamera Anda.
                        </div>

                        <div style="margin-top: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
                            <button type="button" @click="startCamera()" style="background: #1f9d55; color: #ffffff; border: none; padding: 6px 14px; border-radius: 8px; font-size: 0.775rem; font-weight: 600; cursor: pointer;">
                                📹 Nyalakan Kamera
                            </button>
                            <label style="background: rgba(255,255,255,0.08); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.15); padding: 6px 14px; border-radius: 8px; font-size: 0.775rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                📁 Upload Foto
                                <input type="file" accept="image/*" capture="user" @change="handleFileUpload($event)" style="display: none;">
                            </label>
                        </div>
                    </div>

                    <!-- Watermark Timestamp -->
                    <div class="att-watermark" x-show="isCameraOn || capturedPhoto">
                        <x-heroicon-m-shield-check style="width: 1rem; height: 1rem; color: #10b981;" />
                        <span x-text="currentLiveTime">{{ now()->format('d M Y - H:i:s') }} WIB</span>
                        <span>|</span>
                        <span x-text="distanceText"></span>
                    </div>
                </div>

                <!-- Kontrol Kamera & Tombol Presensi -->
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(128,128,128,0.15);">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <x-filament::button 
                            x-show="isCameraOn && !capturedPhoto" 
                            @click="takeSnapshot()" 
                            icon="heroicon-m-camera" 
                            color="info">
                            Ambil Foto Selfie
                        </x-filament::button>

                        <x-filament::button 
                            x-show="capturedPhoto" 
                            @click="retakeSnapshot()" 
                            icon="heroicon-m-arrow-uturn-left" 
                            color="gray">
                            Foto Ulang
                        </x-filament::button>

                        <label x-show="isCameraOn && !capturedPhoto" style="cursor: pointer;">
                            <span style="font-size: 0.75rem; color: #94a3b8; text-decoration: underline;">Atau upload file</span>
                            <input type="file" accept="image/*" capture="user" @change="handleFileUpload($event)" style="display: none;">
                        </label>
                    </div>

                    <div style="display: flex; gap: 0.75rem;">
                        <!-- Tombol Clock In -->
                        <x-filament::button 
                            @click="submitClockIn()" 
                            icon="heroicon-m-arrow-right-start-on-rectangle" 
                            color="success">
                            Catat Presensi Masuk (Clock In)
                        </x-filament::button>

                        <!-- Tombol Clock Out -->
                        @if($todayAttendance && !$todayAttendance->clock_out)
                            <x-filament::button 
                                @click="submitClockOut()" 
                                icon="heroicon-m-arrow-left-start-on-rectangle" 
                                color="info">
                                Presensi Pulang (Clock Out)
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        </div>
    </div>

    <!-- Alpine.js Geolocation & Camera Script -->
    <script>
        function attendanceTracker() {
            return {
                isCameraOn: false,
                capturedPhoto: null,
                cameraStatusTitle: 'Kamera Belum Aktif',
                cameraStatusDesc: 'Klik tombol "Nyalakan Kamera" atau izinkan browser mengakses kamera Anda.',
                mediaStream: null,
                userLat: null,
                userLong: null,
                branchLat: {{ $branch?->latitude ?? -6.21462 }},
                branchLong: {{ $branch?->longitude ?? 106.82142 }},
                allowedRadius: {{ $branch?->radius_meters ?? 50 }},
                distanceMeters: null,
                isWithin: false,
                locationStatus: 'Mendeteksi koordinat...',
                distanceText: 'Menghitung...',
                currentLiveTime: '',
                timeInterval: null,

                init() {
                    this.updateLiveTime();
                    this.timeInterval = setInterval(() => {
                        this.updateLiveTime();
                    }, 1000);
                    this.trackLocation();
                    this.$nextTick(() => {
                        this.startCamera();
                    });
                },

                updateLiveTime() {
                    const now = new Date();
                    const day = String(now.getDate()).padStart(2, '0');
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const month = months[now.getMonth()];
                    const year = now.getFullYear();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');

                    this.currentLiveTime = `${day} ${month} ${year} - ${hours}:${minutes}:${seconds} WIB`;
                },

                trackLocation() {
                    if ("geolocation" in navigator) {
                        navigator.geolocation.watchPosition(
                            (position) => {
                                this.userLat = position.coords.latitude;
                                this.userLong = position.coords.longitude;
                                this.calculateDistance();
                                @this.updateCoordinates(this.userLat, this.userLong);
                            },
                            (error) => {
                                console.warn('Geolocation error:', error);
                                this.locationStatus = 'GPS Tidak Aktif / Ditolak';
                                this.distanceText = 'Error';
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    } else {
                        this.locationStatus = 'Browser tidak mendukung GPS';
                    }
                },

                calculateDistance() {
                    if (!this.userLat || !this.userLong) return;

                    const R = 6371000; // Radius bumi dalam meter
                    const dLat = (this.branchLat - this.userLat) * Math.PI / 180;
                    const dLon = (this.branchLong - this.userLong) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                              Math.cos(this.userLat * Math.PI / 180) * Math.cos(this.branchLat * Math.PI / 180) *
                              Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    const d = Math.round(R * c);

                    this.distanceMeters = d;
                    this.isWithin = d <= this.allowedRadius;
                    this.distanceText = d + ' meter';

                    if (this.isWithin) {
                        this.locationStatus = 'Dalam Radius Kantor';
                    } else {
                        this.locationStatus = 'Di Luar Area Kantor (' + d + 'm)';
                    }
                },

                async startCamera() {
                    this.cameraStatusTitle = 'Menghubungkan Kamera...';
                    this.cameraStatusDesc = 'Mohon tunggu atau klik "Izinkan" jika muncul popup izin kamera di browser.';

                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        this.cameraStatusTitle = 'Kamera Tidak Tersedia';
                        this.cameraStatusDesc = 'Browser Anda membatasi akses webcam pada koneksi ini. Anda dapat menggunakan tombol "Upload Foto" sebagai alternatif.';
                        return;
                    }

                    // Stop previous stream if any
                    if (this.mediaStream) {
                        this.mediaStream.getTracks().forEach(track => track.stop());
                    }

                    try {
                        // First attempt: with facingMode user (ideal for selfie / mobile)
                        let stream;
                        try {
                            stream = await navigator.mediaDevices.getUserMedia({ 
                                video: { facingMode: 'user' }, 
                                audio: false 
                            });
                        } catch (e) {
                            // Fallback attempt: generic video (for desktop webcams without facingMode)
                            stream = await navigator.mediaDevices.getUserMedia({ 
                                video: true, 
                                audio: false 
                            });
                        }

                        this.mediaStream = stream;
                        if (this.$refs.videoElement) {
                            this.$refs.videoElement.srcObject = stream;
                            this.$refs.videoElement.onloadedmetadata = () => {
                                this.$refs.videoElement.play().catch(e => console.warn(e));
                            };
                        }
                        this.isCameraOn = true;
                    } catch (err) {
                        console.warn('Camera access error:', err);
                        this.isCameraOn = false;
                        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                            this.cameraStatusTitle = 'Izin Kamera Ditolak';
                            this.cameraStatusDesc = 'Silakan klik ikon gembok / kamera di sebelah kiri URL address bar browser, lalu ubah status izin kamera menjadi "Izinkan / Allow", kemudian refresh.';
                        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                            this.cameraStatusTitle = 'Kamera Tidak Ditemukan';
                            this.cameraStatusDesc = 'Tidak ada perangkat webcam yang terdeteksi. Anda dapat menggunakan tombol "Upload Foto".';
                        } else {
                            this.cameraStatusTitle = 'Kamera Belum Aktif';
                            this.cameraStatusDesc = 'Gagal memuat video (' + (err.message || err.name) + '). Klik "Nyalakan Kamera" atau gunakan tombol "Upload Foto".';
                        }
                    }
                },

                takeSnapshot() {
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.canvasElement;
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.capturedPhoto = e.target.result;
                        this.isCameraOn = false;
                    };
                    reader.readAsDataURL(file);
                },

                retakeSnapshot() {
                    this.capturedPhoto = null;
                    this.startCamera();
                },

                simulateAtOffice() {
                    this.userLat = this.branchLat;
                    this.userLong = this.branchLong;
                    this.calculateDistance();
                    @this.updateCoordinates(this.userLat, this.userLong);
                },

                submitClockIn() {
                    @this.clockIn(this.capturedPhoto || null, this.userLat, this.userLong);
                },

                submitClockOut() {
                    @this.clockOut(this.userLat, this.userLong);
                },

                destroy() {
                    if (this.timeInterval) {
                        clearInterval(this.timeInterval);
                    }
                    if (this.mediaStream) {
                        this.mediaStream.getTracks().forEach(track => track.stop());
                    }
                }
            }
        }
    </script>
</x-filament-panels::page>
