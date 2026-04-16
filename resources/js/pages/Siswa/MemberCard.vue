<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download, ArrowLeft, IdCard } from 'lucide-vue-next';
import SiswaLayout from '@/layouts/SiswaLayout.vue';

interface Member {
    id: number;
    no_anggota: string;
    kelas: string | null;
    alamat: string | null;
    telepon: string | null;
    tanggal_lahir: string | null;
    jenis_kelamin: string | null;
    status: string;
    foto_url: string | null;
    created_at: string;
    user: { name: string; email: string };
}

const props = defineProps<{ member: Member }>();

const formatDate = (d: string | null) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

// ── jsPDF Download ──────────────────────────────────────────────────
const downloadPDF = async () => {
    const { jsPDF } = await import('jspdf');
    const doc = new jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: [85.6, 54],
    });

    // Background gradient simulation
    doc.setFillColor(30, 64, 175);
    doc.rect(0, 0, 85.6, 54, 'F');

    // Darker header bar
    doc.setFillColor(15, 40, 130);
    doc.rect(0, 0, 85.6, 9, 'F');

    // Header text
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(5.5);
    doc.setFont('helvetica', 'bold');
    doc.text('KARTU ANGGOTA PERPUSTAKAAN', 42.8, 5.5, { align: 'center' });

    // Photo placeholder / actual photo
    if (props.member.foto_url) {
        try {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            await new Promise<void>((resolve, reject) => {
                img.onload = () => resolve();
                img.onerror = () => reject();
                img.src = props.member.foto_url!;
            });
            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            canvas.getContext('2d')!.drawImage(img, 0, 0);
            doc.addImage(canvas.toDataURL('image/jpeg'), 'JPEG', 4, 11, 18, 22, undefined, 'FAST');
        } catch {
            // fallback box
            doc.setFillColor(255, 255, 255, 0.2);
            doc.roundedRect(4, 11, 18, 22, 2, 2, 'F');
        }
    } else {
        doc.setFillColor(255, 255, 255);
        doc.setFillColor(80, 120, 220);
        doc.roundedRect(4, 11, 18, 22, 2, 2, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text(props.member.user.name.charAt(0).toUpperCase(), 13, 24, { align: 'center' });
    }

    // Member info
    const infoX = 26;
    doc.setFontSize(9);
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(255, 255, 255);
    doc.text(props.member.user.name, infoX, 15);

    doc.setFontSize(6);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(200, 210, 255);
    doc.text(props.member.no_anggota, infoX, 19);

    if (props.member.kelas) {
        doc.setFillColor(255, 255, 255, 0.2);
        doc.setFillColor(50, 90, 200);
        doc.roundedRect(infoX, 21, 22, 4, 1, 1, 'F');
        doc.setFontSize(5);
        doc.setTextColor(255, 255, 255);
        doc.text(props.member.kelas, infoX + 11, 23.7, { align: 'center' });
    }

    const jkLabel = props.member.jenis_kelamin === 'L' ? 'Laki-laki' : props.member.jenis_kelamin === 'P' ? 'Perempuan' : '';
    if (jkLabel) {
        doc.setFillColor(50, 90, 200);
        doc.roundedRect(infoX + 24, 21, 18, 4, 1, 1, 'F');
        doc.setFontSize(5);
        doc.setTextColor(255, 255, 255);
        doc.text(jkLabel, infoX + 33, 23.7, { align: 'center' });
    }

    doc.setFontSize(5);
    doc.setTextColor(180, 195, 255);
    doc.text(`Berlaku sejak: ${formatDate(props.member.created_at)}`, infoX, 28.5);

    // Footer bar
    doc.setFillColor(10, 30, 100);
    doc.rect(0, 47, 85.6, 7, 'F');
    doc.setFontSize(5);
    doc.setTextColor(180, 195, 255);
    doc.text(`Status: ${props.member.status === 'aktif' ? 'AKTIF' : 'NON-AKTIF'}`, 82, 51.5, { align: 'right' });

    doc.save(`kartu-${props.member.no_anggota}.pdf`);
};

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
    { title: 'Kartu Anggota', href: '/siswa/kartu' },
];
</script>

<template>
    <Head title="Kartu Anggota Saya" />

    <SiswaLayout>
        <div class="flex h-full flex-1 flex-col items-center gap-6 p-6">

            <!-- Title -->
            <div class="w-full max-w-xl text-center">
                <h1 class="text-2xl font-bold" style="color: #5C3D1E; font-family: Georgia, serif;">Kartu Anggota Saya</h1>
                <p class="text-sm mt-1" style="color: #9A7050;">Kartu digital perpustakaan Anda</p>
            </div>

            <!-- Card Visual -->
            <div class="w-full max-w-xs">
                <div class="card-front">
                    <!-- Header -->
                    <div class="card-header">
                        <IdCard class="h-4 w-4 opacity-80" />
                        <span>KARTU ANGGOTA PERPUSTAKAAN</span>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <!-- Photo -->
                        <div class="card-photo">
                            <img v-if="member.foto_url" :src="member.foto_url" class="card-photo-img" alt="Foto" />
                            <div v-else class="card-photo-placeholder">
                                {{ member.user.name.charAt(0).toUpperCase() }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="card-info">
                            <p class="card-name">{{ member.user.name }}</p>
                            <p class="card-no">{{ member.no_anggota }}</p>
                            <div class="card-pills">
                                <span v-if="member.kelas" class="card-pill">{{ member.kelas }}</span>
                                <span v-if="member.jenis_kelamin" class="card-pill">
                                    {{ member.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </div>
                            <p class="card-tgl">Bergabung: {{ formatDate(member.created_at) }}</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer">
                        Status: <strong class="text-green-300">{{ member.status === 'aktif' ? 'AKTIF' : 'NON-AKTIF' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <Link
                    href="/siswa"
                    class="inline-flex items-center gap-2 rounded-lg border-2 px-4 py-2.5 text-sm font-bold transition-all hover:bg-orange-50"
                    style="border-color: #E8A020; color: #5C3D1E;"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
                <button
                    @click="downloadPDF"
                    class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                    style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                >
                    <Download class="h-4 w-4" />
                    Download PDF
                </button>
            </div>

            <!-- Info box -->
            <div class="max-w-xs rounded-xl border p-4 text-center" style="border-color: #F0D6A8; background: #FFF8F0;">
                <p class="text-xs" style="color: #9A7050;">
                    💡 Tunjukkan kartu ini kepada petugas perpustakaan saat meminjam buku.
                </p>
            </div>

        </div>
    </SiswaLayout>
</template>

<style scoped>
.card-front {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(92, 61, 30, 0.4);
    background: linear-gradient(135deg, #E8A020 0%, #C4781A 60%, #92400E 100%);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 9px 14px;
    background: rgba(0,0,0,0.25);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: white;
}

.card-body {
    display: flex;
    gap: 14px;
    padding: 14px;
    align-items: center;
}

.card-photo {
    width: 70px;
    height: 80px;
    border-radius: 10px;
    overflow: hidden;
    border: 3px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
    background: rgba(255,255,255,0.1);
}

.card-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: 700;
    color: white;
}

.card-info { flex: 1; min-width: 0; }

.card-name {
    font-size: 14px;
    font-weight: 700;
    color: white;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-no {
    font-size: 10px;
    color: rgba(255,255,255,0.65);
    font-family: monospace;
    letter-spacing: 0.04em;
    margin-bottom: 7px;
}

.card-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 6px;
}

.card-pill {
    font-size: 9px;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(255,255,255,0.2);
    color: white;
    font-weight: 600;
}

.card-tgl {
    font-size: 9px;
    color: rgba(255,255,255,0.5);
}

.card-footer {
    padding: 7px 14px;
    background: rgba(0,0,0,0.25);
    font-size: 9px;
    color: rgba(255,255,255,0.65);
    text-align: right;
}
</style>
