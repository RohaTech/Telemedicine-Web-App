# Tenadam Telemedicine Web Application

A full-featured telemedicine platform enabling patients to consult with doctors online, book appointments, manage medical records, and make secure payments. Includes real-time chat, video calls, and a robust, branded payment and receipt system.

---

## Features

- **User & Doctor Registration/Login**
  - Secure authentication for patients and doctors.

- **Doctor Directory & Booking**
  - Browse doctors by specialty, view profiles, and book appointments.

- **Online Consultations**
  - Video call and real-time chat for seamless doctor-patient interaction.

- **Medical Records**
  - Patients and doctors can view and manage consultation history and prescriptions.

- **Simulated Payment Gateway**
  - Supports card, M-Pesa, and Telebirr payments.
  - Payment amount always matches the doctor’s set consultation fee (from the database).
  - Robust error handling for undefined/null data.
  - Professional PDF receipts with Tenadam branding and logo.

- **Admin Panel**
  - Manage users, doctors, appointments, and system settings.

---

## Tech Stack

- **Frontend:** Vue.js, Vite, Tailwind CSS, jsPDF
- **Backend:** Laravel, PHP, MySQL/SQLite
- **Real-time:** Laravel Echo, Pusher/PeerJS (for chat and video)
- **PDF Generation:** jsPDF

---

## Setup Instructions

### Prerequisites

- Node.js (v16+)
- PHP (v8+)
- Composer
- MySQL or SQLite

### Installation Steps

1. **Clone the Repository**
    ```bash
    git clone <repository-url>
    cd tenadam-telemedicine
    ```

2. **Install Backend Dependencies**
    ```bash
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    ```

3. **Install Frontend Dependencies**
    ```bash
    cd frontend
    npm install
    ```

4. **Run the Application**

    - **Backend:**  
      ```bash
      php artisan serve
      ```

    - **Frontend:**  
      ```bash
      cd frontend
      npm run dev
      ```

    - **PeerJS Server (for video calls):**  
      ```bash
      peerjs --port 9000
      ```

---

## Usage

1. Register as a patient or doctor.
2. Patients can browse doctors, book appointments, and join consultations.
3. Consultations support video calls and real-time chat.
4. Pay for consultations using card, M-Pesa, or Telebirr.
   - The payment amount is always the doctor’s set fee (never a default).
5. After payment, download a branded PDF receipt.
6. Admins can manage users, doctors, and appointments.

---

## Payment System Details

- **Dynamic Fee:**  
  The payment amount is always fetched from the doctor’s profile in the database.

- **Multiple Methods:**  
  Card, M-Pesa, Telebirr (simulated for demo).

- **PDF Receipts:**  
  Receipts use Tenadam branding, colors (`#048581`, `#2FC7A1`), and logo.

- **Robustness:**  
  Handles null/undefined data gracefully; logs errors for debugging.

---

## Real-Time Features

- **Video Calls:**  
  Secure, browser-based video consultations.

- **Chat:**  
  Instant messaging during consultations.

---

## Development Notes

- All code is robust to missing/null data.
- Payment and consultation flows are fully documented in:
  - `DEV_PAYMENT_SYSTEM.md`
  - `PAYMENT_GATEWAY_IMPLEMENTATION.md`
  - `RECEIPT_PDF_IMPLEMENTATION.md`
  - `PAYMENT_AMOUNT_FIX.md`
- Debug logging is enabled throughout the payment and consultation flows.

---

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

## License

MIT

---

## Contact

For questions or support, please contact [rohatechofficial@gmail.com].
