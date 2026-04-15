import fetch from 'node-fetch';

async function test() {
    const res = await fetch('http://localhost:8000/siswa/masuk', {
        headers: {
            'X-Inertia': 'true',
            'X-Inertia-Version': ''
        }
    });
    console.log('Status:', res.status);
    console.log('Headers:', res.headers.raw());
    const text = await res.text();
    console.log('Body:', text.substring(0, 500));
}
test();
