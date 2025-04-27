<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin', 'description' => 'System Administrator']);
        $doctor = Role::create(['name' => 'doctor', 'description' => 'Medical Doctor']);
        $patient = Role::create(['name' => 'patient', 'description' => 'Patient']);
        $customer = Role::create(['name' => 'customer', 'description' => 'Customer']);

        // User & Account Management permissions
        $userPermissions = [
            ['name' => 'create_user', 'group' => 'User & Account Management', 'description' => 'Create new users'],
            ['name' => 'view_user', 'group' => 'User & Account Management', 'description' => 'View user information'],
            ['name' => 'update_user', 'group' => 'User & Account Management', 'description' => 'Update user information'],
            ['name' => 'delete_user', 'group' => 'User & Account Management', 'description' => 'Delete users'],
            ['name' => 'verify_email', 'group' => 'User & Account Management', 'description' => 'Verify email address'],
            ['name' => 'resend_verification_email', 'group' => 'User & Account Management', 'description' => 'Resend verification email'],
        ];

        // Patient & Doctor Profiles permissions
        $profilePermissions = [
            ['name' => 'create_patient_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'Create patient profiles'],
            ['name' => 'view_patient_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'View patient profiles'],
            ['name' => 'update_patient_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'Update patient profiles'],
            ['name' => 'delete_patient_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'Delete patient profiles'],
            ['name' => 'create_doctor_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'Create doctor profiles'],
            ['name' => 'view_doctor_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'View doctor profiles'],
            ['name' => 'update_doctor_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'Update doctor profiles'],
            ['name' => 'delete_doctor_profile', 'group' => 'Patient & Doctor Profiles', 'description' => 'Delete doctor profiles'],
        ];

        // Doctor-Patient Assignments permissions
        $assignmentPermissions = [
            ['name' => 'assign_patient_to_doctor', 'group' => 'Doctor-Patient Assignments', 'description' => 'Assign patients to doctors'],
            ['name' => 'view_doctor_patients', 'group' => 'Doctor-Patient Assignments', 'description' => 'View doctor-patient assignments'],
            ['name' => 'unassign_patient_from_doctor', 'group' => 'Doctor-Patient Assignments', 'description' => 'Unassign patients from doctors'],
        ];

        // Product Management permissions
        $productPermissions = [
            ['name' => 'create_product', 'group' => 'Product Management', 'description' => 'Create new products'],
            ['name' => 'view_product', 'group' => 'Product Management', 'description' => 'View products'],
            ['name' => 'update_product', 'group' => 'Product Management', 'description' => 'Update products'],
            ['name' => 'delete_product', 'group' => 'Product Management', 'description' => 'Delete products'],
        ];

        // Sensor Data Operations permissions
        $sensorPermissions = [
            ['name' => 'record_sensor_reading', 'group' => 'Sensor Data Operations', 'description' => 'Record sensor readings'],
            ['name' => 'view_sensor_reading', 'group' => 'Sensor Data Operations', 'description' => 'View sensor readings'],
            ['name' => 'summarize_sensor_readings', 'group' => 'Sensor Data Operations', 'description' => 'Summarize sensor readings'],
            ['name' => 'export_patient_data', 'group' => 'Sensor Data Operations', 'description' => 'Export patient data'],
            ['name' => 'trigger_alert_notification', 'group' => 'Sensor Data Operations', 'description' => 'Trigger alert notifications'],
        ];

        // Messaging & Alerts permissions
        $messagingPermissions = [
            ['name' => 'send_message', 'group' => 'Messaging & Alerts', 'description' => 'Send messages'],
            ['name' => 'view_messages', 'group' => 'Messaging & Alerts', 'description' => 'View messages'],
            ['name' => 'mark_message_read', 'group' => 'Messaging & Alerts', 'description' => 'Mark messages as read'],
            ['name' => 'delete_message', 'group' => 'Messaging & Alerts', 'description' => 'Delete messages'],
        ];

        // System & Logs permissions
        $systemPermissions = [
            ['name' => 'view_system_logs', 'group' => 'System & Logs', 'description' => 'View system logs'],
            ['name' => 'clear_system_logs', 'group' => 'System & Logs', 'description' => 'Clear system logs'],
            ['name' => 'view_dashboard_metrics', 'group' => 'System & Logs', 'description' => 'View dashboard metrics'],
            ['name' => 'configure_mattress_firmware', 'group' => 'System & Logs', 'description' => 'Configure mattress firmware'],
        ];

        // Security & Access Control permissions
        $securityPermissions = [
            ['name' => 'manage_roles_permissions', 'group' => 'Security & Access Control', 'description' => 'Manage roles and permissions'],
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $profilePermissions,
            $assignmentPermissions,
            $productPermissions,
            $sensorPermissions,
            $messagingPermissions,
            $systemPermissions,
            $securityPermissions
        );

        // Create permissions
        foreach ($allPermissions as $permission) {
            Permission::create($permission);
        }

        // Assign permissions to roles using the role_permission table
        $admin->permissions()->attach(Permission::all());

        // Doctor permissions
        $doctor->permissions()->attach(Permission::whereIn('name', [
            'view_user',
            'update_user',
            'create_patient_profile',
            'view_patient_profile',
            'update_patient_profile',
            'create_doctor_profile',
            'view_doctor_profile',
            'update_doctor_profile',
            'assign_patient_to_doctor',
            'view_doctor_patients',
            'unassign_patient_from_doctor',
            'view_product',
            'view_sensor_reading',
            'summarize_sensor_readings',
            'export_patient_data',
            'trigger_alert_notification',
            'send_message',
            'view_messages',
            'mark_message_read',
            'delete_message',
            'view_dashboard_metrics'
        ])->get());

        // Patient permissions
        $patient->permissions()->attach(Permission::whereIn('name', [
            'view_user',
            'update_user',
            'verify_email',
            'resend_verification_email',
            'view_patient_profile',
            'update_patient_profile',
            'view_product',
            'record_sensor_reading',
            'view_sensor_reading',
            'export_patient_data',
            'send_message',
            'view_messages',
            'mark_message_read',
            'delete_message'
        ])->get());

        // Customer permissions
        $customer->permissions()->attach(Permission::whereIn('name', [
            'create_user',
            'view_user',
            'update_user',
            'verify_email',
            'resend_verification_email',
            'view_product',
            'send_message',
            'view_messages',
            'mark_message_read',
            'delete_message'
        ])->get());
    }
}
