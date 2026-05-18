import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp, usePage } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import "../css/app.css";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { watch } from "vue";

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.mount(el);

        // --- GLOBAL SWEETALERT LOGIC ---
        // Monitor Flash Messages
        watch(
            () => usePage().props.flash,
            (flash) => {
                if (flash && flash.success) {
                    Swal.fire({
                        title: "BERHASIL!",
                        text: flash.success,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                        background: "#ffffff",
                        color: "#1e293b",
                        iconColor: "#4f46e5",
                        customClass: {
                            popup: "rounded-[2rem] shadow-2xl border border-slate-100",
                        },
                    });
                }
                if (flash && flash.error) {
                    Swal.fire({
                        title: "GAGAL!",
                        text: flash.error,
                        icon: "error",
                        background: "#ffffff",
                        color: "#1e293b",
                        confirmButtonColor: "#f43f5e",
                        customClass: {
                            popup: "rounded-[2rem] shadow-2xl border border-slate-100",
                        },
                    });
                }
            },
            { deep: true },
        );

        // Monitor Validation Errors
        watch(
            () => usePage().props.errors,
            (errors) => {
                if (errors && Object.keys(errors).length > 0) {
                    const firstError = Object.values(errors)[0];
                    alert("DEBUG ERROR: " + firstError);
                    Swal.fire({
                        title: "PERIKSA KEMBALI",
                        text: firstError,
                        icon: "warning",
                        background: "#ffffff",
                        color: "#1e293b",
                        confirmButtonColor: "#4f46e5",
                        customClass: {
                            popup: "rounded-[2rem] shadow-2xl border border-slate-100",
                        },
                    });
                }
            },
            { deep: true },
        );
    },
});
