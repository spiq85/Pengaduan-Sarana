# Feature Summary - Aspirasi Siswa Web Application

## Overview
Aplikasi web untuk mengelola aspirasi/keluhan sarana siswa ke sekolah dengan sistem approval admin, progress tracking, dan fitur rating/feedback.

---

## Core Features

### 1. **Authentication & Authorization**
- **Student Login/Logout**
  - Login dengan username + password
  - Session management untuk akses halaman student
  
- **Admin Panel**
  - Admin untuk approval + progress management

---

### 2. **Student Features**

#### A. **Aspirasiku Workspace (AI-Style Chat Interface)**
- **Left Sidebar (History)**
  - List semua aspirasi milik siswa
  - Status badge: menunggu / diterima / ditolak
  - Mode indicator: Template / Custom Priority
  - Custom priority rank display (#1, #2, dst)
  
- **Right Panel (Compose/Detail)**
  - **New Chat / Compose Mode:**
    - Step-by-step form (AI chatbot style)
    - **Template Mode:**
      - Title (required)
      - Kategori resmi (required) - pilihan dari list
      - Lokasi resmi (required) - pilihan dari sistem
      - Description (required)
      - Foto bukti (optional)
    - **Custom Mode:**
      - Title (required)
      - Lokasi bebas (required) - user input
      - Konteks prioritas (required) - user explanation
      - Description (required)
      - Foto bukti (optional)
      - **Priority:** Berdasarkan input_at (earliest = highest priority)
  
  - **Detail Thread Mode:**
    - Student submission bubble (right side, blue)
    - Admin responses (left side, gray)
    - Progress updates (left side, green) - ketika aspirasi dalam proses
    - Feedback bubbles (left side, yellow/amber)
    - Rating form (ketika status = Selesai)

#### B. **Global Dashboard (Voting Feed)**
- **Show:** Template aspirations yang sudah approved (status = diterima)
- **Hidden:** Custom aspirations meski approved (untuk SLA consistency)
- **Features:**
  - Vote up aspirasi (like/thumbs up)
  - Lihat voting count
  - Filter by kategori
  - View details

#### C. **Rating & Feedback (Post-Completion)**
- **When Available:**
  - Hanya ketika aspirasi status = diterima AND progress_status = Selesai
  
- **Form Fields:**
  - Rating score: 1-5 bintang
  - Feedback text: Komentar optional
  
- **Storage:**
  - Disimpan di `input_aspirations.rating` dan `.feedback`
  - Bisa di-update berkali-kali

#### D. **Notification System**
- Real-time notifications ketika:
  - Aspirasi disetujui admin
  - Ada progress update
  - Aspirasi ditolak + alasan
  - Ada feedback baru

#### E. **Student Profile**
- Lihat info personal
- Lihat history aspirasi di profile

---

### 3. **Admin Features**

#### A. **Aspirasi List & Filtering**
- **Columns:**
  - ID
  - Vote count (priority by votes)
  - Siswa (username)
  - Title
  - Mode (Template / Custom)
  - Kategori
  - Lokasi
  - **Prioritas Custom:** 
    - Show: "ANTREAN #1", "ANTREAN #2" dst (berbeda warna)
    - Ketika mode filter = custom
    - Warna highlight untuk #1 (red), #2 (orange), others (lighter red)
    - Plus waktu masuk (jam:menit)
  - Status (menunggu / diterima / ditolak)
  - Progress status (Belum Dimulai / Dalam Proses / Selesai)
  - Tanggal

- **Filters:**
  - Search (lokasi/deskripsi)
  - Date range (from/to)
  - Kategori
  - Status pengajuan
  - Mode aspirasi (Template / Custom)
  - Progress status
  - Sort: Terbaru / Prioritas (vote count)

- **Actions:**
  - View Detail
  - Approve → Buat record di aspirations table, set status = diterima
  - Reject → Set status = ditolak + rejection reason
  - Export Excel
  - Export PDF

#### B. **Aspirasi Detail & Approval**
- View full aspirasi details
- If status = menunggu:
  - Approve button + optional admin message
  - Reject button + rejection reason required
  
#### C. **Progress Management**
- Track aspirasi progress (3 stages):
  - Belum Dimulai (initial)
  - Dalam Proses (started)
  - Selesai (completed)
  
- Update form:
  - Progress status dropdown
  - Progress description (text)
  - Progress evidence image (upload)

#### D. **Feedback Management**
- Add feedback/update messages ke aspirasi
- Visible to student in their thread

#### E. **Voting Statistics**
- See aspirasi ranked by vote count
- High priority alert (≥10 votes)
- Need attention flag (≥5 votes)

#### F. **Export Features**
- Export to Excel dengan filters applied
- Export to PDF (landscape orientation)
- Stats included: Total, Menunggu, Diterima, Ditolak

---

### 4. **Data Models**

#### Input Aspirations
```
id_input, input_by, input_at, id_category, id_location, room_number,
title, description, location, image, submission_mode, 
custom_location, custom_context, submission_status,
admin_message, rating, feedback, created_at, updated_at
```

#### Aspirations (created when approved)
```
id_aspiration, id_input, progress_status, progress_evidence_image,
progress_description, priority_level, start_at, end_at, deadline,
created_at, updated_at
```

#### Categories
```
id_category, category_name, description
```

#### Locations
```
id_location, location_name, location_type (kelas/ruang/fasilitas), is_active
```

#### Votes
```
id_vote, aspiration_id, student_id, created_at
```

#### Feedbacks
```
id_feedback, id_aspiration, user_id, message, feedback_at
```

---

## Business Logic

### Approval Flow
1. Student submit aspirasi → status: menunggu
2. Admin approve → create Aspirations record, status: diterima
3. Admin update progress → progress tracking
4. Admin set status Selesai → student can rate
5. Student rate/feedback → record rating + feedback

### Template vs Custom Mode
| Aspect | Template | Custom |
|--------|----------|--------|
| Category | Required, from list | Auto-assigned |
| Location | Required, from system | Free text (custom_location) |
| Context | - | Requires explanation |
| SLA Tracking | Yes | No |
| Global Dashboard | Yes (if approved) | No (approved or not) |
| Priority | By votes | By input_at (FIFO) |
| Admin Panel | Mixed | Separate view when filtered |

### Vote System
- Student vote up approved aspirations
- Vote count affects priority ranking
- Used for high-priority alert (≥10 votes)

---

## User Journeys

### Student: Submit Template Aspirasi
1. Click "Aspirasiku" workspace
2. Click "New Chat" button
3. Choose "Template" mode
4. Fill title → Select category → Select location → Write description → Upload photo
5. Submit
6. Aspirasi appears in history with "menunggu" status
7. When approved by admin, gets notification
8. Can see thread with admin updates + progress
9. When progress = Selesai, can rate

### Student: Submit Custom Aspirasi
1. Click "Aspirasiku" workspace
2. Click "New Chat" button
3. Choose "Custom" mode
4. Fill title → Enter custom location → Explain priority → Write description → Upload photo
5. Submit
6. Aspirasi queued by input_at (NOT visible in global dashboard)
7. Admin sees with ANTREAN #X ranking
8. When approved, same flow as template

### Admin: Manage Aspirations
1. Go to Admin Panel → Aspirasi Siswa
2. Filter by mode (Template / Custom) to see separate queues
3. For Custom: See ANTREAN #1, #2, etc with time stamps
4. Approve → Add admin message (optional)
5. Update progress with descriptions + evidence photos
6. Aspirasi marked Selesai
7. View student ratings

---

## Key Technical Points

- **Frontend:** Vue 3 + Inertia.js (SPA-style with page reload)
- **Backend:** Laravel 11 with policy-based authorization
- **Database:** MySQL with relationships
- **Rating:** Stored in input_aspirations table (rating + feedback columns)
- **Custom Priority:** Calculated from global input_at ordering
- **Global Feed Exclusion:** whereHas filter on submission_mode = template
