export async function refreshCsrfToken() {
    await axios.get("/sanctum/csrf-cookie");
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    return csrfToken;
}