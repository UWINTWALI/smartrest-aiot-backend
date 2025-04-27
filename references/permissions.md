Perfect! Below is a **grouped list of permissions** organized by **functional area**, showing which **roles** are responsible for handling each permission in the **SmartRest AIoT** system.

---

## 🗃️ **Grouped Permissions and Responsible Roles**

---

### 👤 **User & Account Management**
| **Permission**               | **Handled By**     |
|-----------------------------|--------------------|
| `create_user`               | Admin, Customer (self) |
| `view_user`                 | Admin, Doctor (patients), Patient/Customer (self) |
| `update_user`               | Admin, Doctor/Patient/Customer (self) |
| `delete_user`               | Admin              |
| `verify_email`              | All Roles (self)   |
| `resend_verification_email`| Admin, All Roles (self) |

---

### 🧑‍⚕️ **Patient & Doctor Profiles**
| **Permission**               | **Handled By**     |
|-----------------------------|--------------------|
| `create_patient_profile`    | Admin, Doctor      |
| `view_patient_profile`      | Admin, Doctor (assigned), Patient (self) |
| `update_patient_profile`    | Admin, Doctor (assigned), Patient (self) |
| `delete_patient_profile`    | Admin              |
| `create_doctor_profile`     | Admin, Doctor (self) |
| `view_doctor_profile`       | Admin, Doctor (self) |
| `update_doctor_profile`     | Admin, Doctor (self) |
| `delete_doctor_profile`     | Admin              |

---

### 🏥 **Doctor-Patient Assignments**
| **Permission**                  | **Handled By**     |
|--------------------------------|--------------------|
| `assign_patient_to_doctor`     | Admin, Doctor      |
| `view_doctor_patients`         | Admin, Doctor      |
| `unassign_patient_from_doctor` | Admin, Doctor      |

---

### 📦 **Product (Smart Mattress) Management**
| **Permission**          | **Handled By**     |
|-------------------------|--------------------|
| `create_product`        | Admin              |
| `view_product`          | All Roles          |
| `update_product`        | Admin              |
| `delete_product`        | Admin              |

---

### 📊 **Sensor Data Operations**
| **Permission**              | **Handled By**         |
|-----------------------------|------------------------|
| `record_sensor_reading`     | Admin, Patient (device-based) |
| `view_sensor_reading`       | Admin, Doctor (assigned), Patient (self) |
| `summarize_sensor_readings` | Admin, Doctor          |
| `export_patient_data`       | Admin, Doctor, Patient (self) |
| `trigger_alert_notification`| Admin, Doctor          |

---

### 💬 **Messaging & Alerts**
| **Permission**        | **Handled By**         |
|-----------------------|------------------------|
| `send_message`        | All Roles              |
| `view_messages`       | All Roles              |
| `mark_message_read`   | All Roles              |
| `delete_message`      | Admin, All Roles (own messages only) |

---

### 🛠️ **System & Logs**
| **Permission**          | **Handled By**     |
|-------------------------|--------------------|
| `view_system_logs`      | Admin              |
| `clear_system_logs`     | Admin              |
| `view_dashboard_metrics`| Admin, Doctor      |
| `configure_mattress_firmware` | Admin        |

---

### 🔐 **Security & Access Control**
| **Permission**             | **Handled By**     |
|----------------------------|--------------------|
| `manage_roles_permissions` | Admin              |

---

Would you like this list exported as a spreadsheet or turned into a permissions-policy document format (e.g., for system design docs or API access)?Perfect! Below is a **grouped list of permissions** organized by **functional area**, showing which **roles** are responsible for handling each permission in the **SmartRest AIoT** system.

---

## 🗃️ **Grouped Permissions and Responsible Roles**

---

### 👤 **User & Account Management**
| **Permission**               | **Handled By**     |
|-----------------------------|--------------------|
| `create_user`               | Admin, Customer (self) |
| `view_user`                 | Admin, Doctor (patients), Patient/Customer (self) |
| `update_user`               | Admin, Doctor/Patient/Customer (self) |
| `delete_user`               | Admin              |
| `verify_email`              | All Roles (self)   |
| `resend_verification_email`| Admin, All Roles (self) |

---

### 🧑‍⚕️ **Patient & Doctor Profiles**
| **Permission**               | **Handled By**     |
|-----------------------------|--------------------|
| `create_patient_profile`    | Admin, Doctor      |
| `view_patient_profile`      | Admin, Doctor (assigned), Patient (self) |
| `update_patient_profile`    | Admin, Doctor (assigned), Patient (self) |
| `delete_patient_profile`    | Admin              |
| `create_doctor_profile`     | Admin, Doctor (self) |
| `view_doctor_profile`       | Admin, Doctor (self) |
| `update_doctor_profile`     | Admin, Doctor (self) |
| `delete_doctor_profile`     | Admin              |

---

### 🏥 **Doctor-Patient Assignments**
| **Permission**                  | **Handled By**     |
|--------------------------------|--------------------|
| `assign_patient_to_doctor`     | Admin, Doctor      |
| `view_doctor_patients`         | Admin, Doctor      |
| `unassign_patient_from_doctor` | Admin, Doctor      |

---

### 📦 **Product (Smart Mattress) Management**
| **Permission**          | **Handled By**     |
|-------------------------|--------------------|
| `create_product`        | Admin              |
| `view_product`          | All Roles          |
| `update_product`        | Admin              |
| `delete_product`        | Admin              |

---

### 📊 **Sensor Data Operations**
| **Permission**              | **Handled By**         |
|-----------------------------|------------------------|
| `record_sensor_reading`     | Admin, Patient (device-based) |
| `view_sensor_reading`       | Admin, Doctor (assigned), Patient (self) |
| `summarize_sensor_readings` | Admin, Doctor          |
| `export_patient_data`       | Admin, Doctor, Patient (self) |
| `trigger_alert_notification`| Admin, Doctor          |

---

### 💬 **Messaging & Alerts**
| **Permission**        | **Handled By**         |
|-----------------------|------------------------|
| `send_message`        | All Roles              |
| `view_messages`       | All Roles              |
| `mark_message_read`   | All Roles              |
| `delete_message`      | Admin, All Roles (own messages only) |

---

### 🛠️ **System & Logs**
| **Permission**          | **Handled By**     |
|-------------------------|--------------------|
| `view_system_logs`      | Admin              |
| `clear_system_logs`     | Admin              |
| `view_dashboard_metrics`| Admin, Doctor      |
| `configure_mattress_firmware` | Admin        |

---

### 🔐 **Security & Access Control**
| **Permission**             | **Handled By**     |
|----------------------------|--------------------|
| `manage_roles_permissions` | Admin              |

---

Would you like this list exported as a spreadsheet or turned into a permissions-policy document format (e.g., for system design docs or API access)?Perfect! Below is a **grouped list of permissions** organized by **functional area**, showing which **roles** are responsible for handling each permission in the **SmartRest AIoT** system.

---

## 🗃️ **Grouped Permissions and Responsible Roles**

---

### 👤 **User & Account Management**
| **Permission**               | **Handled By**     |
|-----------------------------|--------------------|
| `create_user`               | Admin, Customer (self) |
| `view_user`                 | Admin, Doctor (patients), Patient/Customer (self) |
| `update_user`               | Admin, Doctor/Patient/Customer (self) |
| `delete_user`               | Admin              |
| `verify_email`              | All Roles (self)   |
| `resend_verification_email`| Admin, All Roles (self) |

---

### 🧑‍⚕️ **Patient & Doctor Profiles**
| **Permission**               | **Handled By**     |
|-----------------------------|--------------------|
| `create_patient_profile`    | Admin, Doctor      |
| `view_patient_profile`      | Admin, Doctor (assigned), Patient (self) |
| `update_patient_profile`    | Admin, Doctor (assigned), Patient (self) |
| `delete_patient_profile`    | Admin              |
| `create_doctor_profile`     | Admin, Doctor (self) |
| `view_doctor_profile`       | Admin, Doctor (self) |
| `update_doctor_profile`     | Admin, Doctor (self) |
| `delete_doctor_profile`     | Admin              |

---

### 🏥 **Doctor-Patient Assignments**
| **Permission**                  | **Handled By**     |
|--------------------------------|--------------------|
| `assign_patient_to_doctor`     | Admin, Doctor      |
| `view_doctor_patients`         | Admin, Doctor      |
| `unassign_patient_from_doctor` | Admin, Doctor      |

---

### 📦 **Product (Smart Mattress) Management**
| **Permission**          | **Handled By**     |
|-------------------------|--------------------|
| `create_product`        | Admin              |
| `view_product`          | All Roles          |
| `update_product`        | Admin              |
| `delete_product`        | Admin              |

---

### 📊 **Sensor Data Operations**
| **Permission**              | **Handled By**         |
|-----------------------------|------------------------|
| `record_sensor_reading`     | Admin, Patient (device-based) |
| `view_sensor_reading`       | Admin, Doctor (assigned), Patient (self) |
| `summarize_sensor_readings` | Admin, Doctor          |
| `export_patient_data`       | Admin, Doctor, Patient (self) |
| `trigger_alert_notification`| Admin, Doctor          |

---

### 💬 **Messaging & Alerts**
| **Permission**        | **Handled By**         |
|-----------------------|------------------------|
| `send_message`        | All Roles              |
| `view_messages`       | All Roles              |
| `mark_message_read`   | All Roles              |
| `delete_message`      | Admin, All Roles (own messages only) |

---

### 🛠️ **System & Logs**
| **Permission**          | **Handled By**     |
|-------------------------|--------------------|
| `view_system_logs`      | Admin              |
| `clear_system_logs`     | Admin              |
| `view_dashboard_metrics`| Admin, Doctor      |
| `configure_mattress_firmware` | Admin        |

---

### 🔐 **Security & Access Control**
| **Permission**             | **Handled By**     |
|----------------------------|--------------------|
| `manage_roles_permissions` | Admin              |

---

Would you like this list exported as a spreadsheet or turned into a permissions-policy document format (e.g., for system design docs or API access)?