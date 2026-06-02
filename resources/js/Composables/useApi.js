/**
 * Simple fetch wrapper that always includes CSRF + JSON headers.
 */
export async function apiPost(url, body = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
    })
    return res.json()
}

export async function apiGet(url) {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } })
    return res.json()
}

export async function downloadBlob(url, body, filename) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify(body),
    })
    if (!res.ok) throw new Error('Download failed')
    const blob = await res.blob()
    const a = document.createElement('a')
    a.href = URL.createObjectURL(blob)
    a.download = filename
    a.click()
    URL.revokeObjectURL(a.href)
}
