const BASE_URL = "http://localhost:8000/api";

function getToken() {
	return localStorage.getItem("token") || "";
}

function authHeaders() {
	return { Authorization: `Bearer ${getToken()}` };
}

// ─── AUTH ─────────────────────────────────────────────────────────

export async function apiRegister(name, email, password) {
	const res = await fetch(`${BASE_URL}/register`, {
		method: "POST",
		headers: { "Content-Type": "application/json" },
		body: JSON.stringify({ name, email, password }),
	});
	return res.json();
}

export async function apiLogin(email, password) {
	const res = await fetch(`${BASE_URL}/login`, {
		method: "POST",
		headers: { "Content-Type": "application/json" },
		body: JSON.stringify({ email, password }),
	});
	return res.json();
}

export async function apiLogout() {
	const res = await fetch(`${BASE_URL}/logout`, {
		method: "POST",
		headers: authHeaders(),
	});
	return res.json();
}

export async function apiMe() {
	const res = await fetch(`${BASE_URL}/me`, { headers: authHeaders() });
	return res.json();
}

// ─── SPOTS (PUBLIC) ───────────────────────────────────────────────

export async function apiGetSpots() {
	const res = await fetch(`${BASE_URL}/spots`);
	return res.json();
}

export async function apiGetSpot(id) {
	const res = await fetch(`${BASE_URL}/spots/${id}`);
	return res.json();
}

// ─── SPOTS (ADMIN) ────────────────────────────────────────────────

export async function apiAdminGetSpots() {
	const res = await fetch(`${BASE_URL}/admin/spots`, { headers: authHeaders() });
	return res.json();
}

export async function apiCreateSpot(formData) {
	const res = await fetch(`${BASE_URL}/spots`, {
		method: "POST",
		headers: authHeaders(),
		body: formData,
	});
	return res.json();
}

export async function apiUpdateSpot(id, formData) {
	formData.append("_method", "POST");
	const res = await fetch(`${BASE_URL}/spots/${id}`, {
		method: "POST",
		headers: authHeaders(),
		body: formData,
	});
	return res.json();
}

export async function apiDeleteSpot(id) {
	const res = await fetch(`${BASE_URL}/spots/${id}`, {
		method: "DELETE",
		headers: authHeaders(),
	});
	return res.json();
}

// ─── REGISTRATIONS ────────────────────────────────────────────────

export async function apiRegisterForSpot(spotId) {
	const res = await fetch(`${BASE_URL}/spots/${spotId}/register`, {
		method: "POST",
		headers: authHeaders(),
	});
	return res.json();
}

export async function apiMyRegistrations() {
	const res = await fetch(`${BASE_URL}/my-registrations`, { headers: authHeaders() });
	return res.json();
}

export async function apiCancelRegistration(spotId) {
	const res = await fetch(`${BASE_URL}/spots/${spotId}/cancel`, {
		method: "PATCH",
		headers: authHeaders(),
	});
	return res.json();
}

export async function apiSpotRegistrations(spotId) {
	const res = await fetch(`${BASE_URL}/spots/${spotId}/registrations`, {
		headers: authHeaders(),
	});
	return res.json();
}

export async function apiConfirmRegistration(registrationId) {
	const res = await fetch(`${BASE_URL}/registrations/${registrationId}/confirm`, {
		method: "PUT",
		headers: authHeaders(),
	});
	return res.json();
}
