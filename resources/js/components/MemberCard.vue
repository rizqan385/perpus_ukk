<script setup lang="ts">
import { ref } from 'vue'
import jsPDF from 'jspdf'

interface Member {
    name: string
    no_anggota: string
    foto?: string
}

const props = defineProps<{
    member: Member
}>()

const cardRef = ref<HTMLDivElement | null>(null)

async function exportPDF() {
    const doc = new jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: [85.6, 54] // ukuran kartu standar (ID Card)
    })

    // Background kartu
    doc.setFillColor(30, 64, 175) // biru
    doc.rect(0, 0, 85.6, 54, 'F')

    // Header
    doc.setTextColor(255, 255, 255)
    doc.setFontSize(10)
    doc.setFont('helvetica', 'bold')
    doc.text('KARTU ANGGOTA PERPUSTAKAAN', 42.8, 10, { align: 'center' })

    // Foto (jika ada)
    if (props.member.foto) {
        doc.addImage(props.member.foto, 'JPEG', 5, 15, 20, 25)
    }

    // Data anggota
    doc.setFontSize(9)
    doc.setFont('helvetica', 'normal')
    doc.text('Nama', 30, 20)
    doc.text(`: ${props.member.name}`, 45, 20)

    doc.text('No. Anggota', 30, 28)
    doc.text(`: ${props.member.no_anggota}`, 45, 28)

    // Garis bawah
    doc.setDrawColor(255, 255, 255)
    doc.line(5, 46, 80, 46)

    doc.setFontSize(7)
    doc.text('Kartu ini adalah milik perpustakaan', 42.8, 50, { align: 'center' })

    doc.save(`kartu-${props.member.no_anggota}.pdf`)
}
</script>

<template>
    <div>
        <!-- Preview Kartu -->
        <div
            ref="cardRef"
            class="w-80 h-48 rounded-xl bg-gradient-to-br from-blue-700 to-blue-900 p-4 text-white shadow-lg"
        >
            <p class="text-center text-sm font-bold mb-3">KARTU ANGGOTA PERPUSTAKAAN</p>

            <div class="flex gap-4 items-center">
                <!-- Foto -->
                <div class="w-16 h-20 rounded-lg bg-white/20 overflow-hidden flex items-center justify-center">
                    <img
                        v-if="member.foto"
                        :src="member.foto"
                        class="w-full h-full object-cover"
                        alt="Foto"
                    />
                    <span v-else class="text-xs text-white/60">No Foto</span>
                </div>

                <!-- Info -->
                <div class="flex flex-col gap-1 text-sm">
                    <div>
                        <p class="text-white/60 text-xs">Nama</p>
                        <p class="font-semibold">{{ member.name }}</p>
                    </div>
                    <div>
                        <p class="text-white/60 text-xs">No. Anggota</p>
                        <p class="font-semibold">{{ member.no_anggota }}</p>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-white/40 mt-3">Kartu ini adalah milik perpustakaan</p>
        </div>

        <!-- Tombol Export -->
        <button
            @click="exportPDF"
            class="mt-4 flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
        >
            Export PDF
        </button>
    </div>
</template>