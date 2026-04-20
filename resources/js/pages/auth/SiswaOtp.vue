<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { BookOpen, ArrowRight, MessageSquareCode } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps<{ 
    status?: string; 
    success?: string;
    phone: string;
    email: string;
    errors?: Record<string, string[] | string>;
}>();

const form = useForm({
    otp: '',
});



// Resend OTP countdown logic
const countdown = ref(60);
let timer: ReturnType<typeof setInterval> | null = null;
const canResend = ref(false);

const startTimer = () => {
    canResend.value = false;
    countdown.value = 60;
    if (timer) clearInterval(timer);
    timer = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            clearInterval(timer!);
            canResend.value = true;
        }
    }, 1000);
};

onMounted(() => {
    startTimer();
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const submitOtp = () => {
    form.post('/siswa/verifikasi-otp');
};

const isResendingWa = ref(false);

const resendOtp = () => {
    isResendingWa.value = true;
    router.post('/siswa/resend-otp', {}, {
        preserveScroll: true,
        onSuccess: () => {
            startTimer();
        },
        onFinish: () => {
            isResendingWa.value = false;
        }
    });
};



const getError = (field: string) => {
    const errs = form.errors as Record<string, string>;
    if (errs[field]) return errs[field];
    if (props.errors && props.errors[field]) {
        return Array.isArray(props.errors[field]) ? props.errors[field][0] : props.errors[field];
    }
    return '';
};
</script>

<template>
    <Head title="Verifikasi OTP — E-Perpustakaan" />

    <div class="flex min-h-screen items-center justify-center p-6" style="background: #FFF8F0; font-family: Georgia, serif;">
        <div class="w-full max-w-md">
            <!-- Logo & Header -->
            <div class="mb-10 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl shadow-xl" 
                     style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                    <MessageSquareCode class="h-8 w-8 text-white" />
                </div>
                <h2 class="text-3xl font-bold" style="color: #5C3D1E;">Verifikasi WhatsApp</h2>
                <p class="mt-3 text-sm" style="color: #9A7050;">
                    Kami telah mengirimkan 6 digit kode OTP ke nomor WhatsApp 
                    <strong class="font-bold text-orange-700">{{ phone }}</strong>.
                </p>
            </div>
            


            <!-- Messages -->
            <div v-if="success" class="mb-6 rounded-xl p-4 text-sm shadow-sm" style="background: #D1FAE5; color: #166534; border: 1px solid #A7F3D0;">
                {{ success }}
            </div>
            <div v-if="getError('error')" class="mb-6 rounded-xl p-4 text-sm shadow-sm" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
                {{ getError('error') }}
            </div>

            <div class="rounded-3xl border-2 bg-white p-8 shadow-sm" style="border-color: #FFE0B2;">
                <form @submit.prevent="submitOtp" class="space-y-6">
                    <div>
                        <label class="mb-2 block text-center text-sm font-semibold" style="color: #5C3D1E;" for="otp">Kode OTP</label>
                        <div class="relative mx-auto mt-2 w-4/5 text-center">
                            <input
                                id="otp"
                                v-model="form.otp"
                                type="text"
                                maxlength="6"
                                required
                                autofocus
                                placeholder="• • • • • •"
                                class="w-full rounded-2xl border-2 bg-white px-4 py-4 text-center text-3xl tracking-[0.5em] outline-none transition-all focus:border-orange-400 focus:shadow-md"
                                :style="(getError('otp') ? 'border-color:#EF4444' : 'border-color:#FFE0B2') + ';color:#1a1a1a;'"
                            />
                        </div>
                        <p v-if="getError('otp')" class="mt-2 text-center text-xs text-red-500">{{ getError('otp') }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing || form.otp.length < 6"
                        class="group mt-8 flex w-full items-center justify-center gap-2 rounded-2xl py-4 text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50"
                        style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                    >
                        <span v-if="form.processing">Memverifikasi...</span>
                        <template v-else>
                            Verifikasi Sekarang
                            <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </template>
                    </button>
                </form>

                <div class="mt-8 border-t pt-6 text-center" style="border-color: #FFF3E0;">
                    <p class="text-sm" style="color: #9A7050;">
                        Belum menerima kode OTP? <br/>
                        <template v-if="canResend">
                            <div class="mt-3 flex items-center justify-center gap-4">
                                <button @click="resendOtp" type="button" :disabled="isResendingWa || isResendingEmail" class="text-sm font-bold transition hover:underline disabled:opacity-50" style="color: #25D366;">
                                    {{ isResendingWa ? 'Mengirim...' : 'Kirim Ulang OTP' }}
                                </button>
                            </div>
                        </template>
                        <span v-else class="mt-2 inline-block text-sm font-semibold opacity-60">
                            Tunggu {{ countdown }} detik untuk kirim ulang
                        </span>
                    </p>
                </div>
            </div>

            <!-- Footer Decorative -->
            <div class="mt-10 flex justify-center opacity-40">
                <BookOpen class="h-6 w-6" style="color: #c4781a" />
            </div>
        </div>
    </div>
</template>
