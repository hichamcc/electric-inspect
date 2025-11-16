<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@electric-app.com',
            'password' => Hash::make('password'),
            'organization_id' => null,
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // Create Organization 1
        $org1 = Organization::create([
            'name' => 'ABC Electrical Services',
            'slug' => 'abc-electrical',
            'address' => '123 Main Street',
            'phone' => '+1-555-0101',
            'email' => 'info@abc-electrical.com',
            'is_active' => true,
        ]);

        // Create users for Organization 1
        $org1Admin = User::create([
            'name' => 'John Smith',
            'email' => 'john@abc-electrical.com',
            'password' => Hash::make('password'),
            'organization_id' => $org1->id,
            'role' => 'organization_admin',
            'email_verified_at' => now(),
        ]);

        $org1Tech = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@abc-electrical.com',
            'password' => Hash::make('password'),
            'organization_id' => $org1->id,
            'role' => 'technician',
            'email_verified_at' => now(),
        ]);

        // Create customers for Organization 1
        $customer1 = Customer::create([
            'organization_id' => $org1->id,
            'customer_id' => 'CUST-' . strtoupper(Str::random(8)),
            'company_name' => 'Tech Corp Industries',
            'address' => '456 Business Blvd',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'contact_person' => 'Robert Johnson',
            'contact_email' => 'robert@techcorp.com',
            'contact_phone' => '+1-555-0201',
            'is_active' => true,
        ]);

        $customer2 = Customer::create([
            'organization_id' => $org1->id,
            'customer_id' => 'CUST-' . strtoupper(Str::random(8)),
            'company_name' => 'Manufacturing Solutions Ltd',
            'address' => '789 Industrial Park',
            'city' => 'Boston',
            'state' => 'MA',
            'postal_code' => '02101',
            'country' => 'USA',
            'contact_person' => 'Sarah Williams',
            'contact_email' => 'sarah@manufacturingsolutions.com',
            'contact_phone' => '+1-555-0202',
            'is_active' => true,
        ]);

        // Create equipment for customers
        $equipment1 = Equipment::create([
            'organization_id' => $org1->id,
            'customer_id' => $customer1->id,
            'equipment_type' => 'Transformer',
            'manufacturer' => 'ABB',
            'model' => 'T-500',
            'serial_number' => 'SN-12345-2022',
            'location' => 'Main Building - Basement',
            'description' => '500 KVA Power Transformer',
            'installation_date' => '2022-06-15',
            'status' => 'active',
        ]);

        $equipment2 = Equipment::create([
            'organization_id' => $org1->id,
            'customer_id' => $customer1->id,
            'equipment_type' => 'Generator',
            'manufacturer' => 'Caterpillar',
            'model' => 'CAT-G1000',
            'serial_number' => 'SN-67890-2021',
            'location' => 'Backup Power Room',
            'description' => '1000 KW Diesel Generator',
            'installation_date' => '2021-03-20',
            'status' => 'active',
        ]);

        $equipment3 = Equipment::create([
            'organization_id' => $org1->id,
            'customer_id' => $customer2->id,
            'equipment_type' => 'Panel Board',
            'manufacturer' => 'Schneider Electric',
            'model' => 'PB-400A',
            'serial_number' => 'SN-11111-2023',
            'location' => 'Production Floor A',
            'description' => '400A Main Distribution Panel',
            'installation_date' => '2023-01-10',
            'status' => 'active',
        ]);

        // Create inspections
        Inspection::create([
            'organization_id' => $org1->id,
            'customer_id' => $customer1->id,
            'equipment_id' => $equipment1->id,
            'inspector_id' => $org1Tech->id,
            'inspection_type' => 'Annual Safety Inspection',
            'inspection_date' => now()->subMonths(2),
            'inspection_time' => '09:00:00',
            'result' => 'Pass - All safety checks passed',
            'notes' => 'Equipment is in good condition. Continue regular maintenance schedule.',
            'status' => 'completed',
        ]);

        Inspection::create([
            'organization_id' => $org1->id,
            'customer_id' => $customer1->id,
            'equipment_id' => $equipment2->id,
            'inspector_id' => $org1Tech->id,
            'inspection_type' => 'Preventive Maintenance',
            'inspection_date' => now()->subMonths(1),
            'inspection_time' => '14:30:00',
            'result' => 'Conditional - Minor issues detected',
            'notes' => 'Minor oil leak detected in cooling system. Replace cooling system seals within 30 days.',
            'status' => 'completed',
        ]);

        Inspection::create([
            'organization_id' => $org1->id,
            'customer_id' => $customer2->id,
            'equipment_id' => $equipment3->id,
            'inspector_id' => $org1Admin->id,
            'inspection_type' => 'Quarterly Inspection',
            'inspection_date' => now()->addDays(7),
            'inspection_time' => '10:00:00',
            'result' => 'Pending',
            'status' => 'scheduled',
        ]);

        // Create Organization 2
        $org2 = Organization::create([
            'name' => 'XYZ Inspection Co',
            'slug' => 'xyz-inspection',
            'address' => '321 Service Road',
            'phone' => '+1-555-0301',
            'email' => 'contact@xyz-inspection.com',
            'is_active' => true,
        ]);

        // Create users for Organization 2
        $org2Admin = User::create([
            'name' => 'Michael Brown',
            'email' => 'michael@xyz-inspection.com',
            'password' => Hash::make('password'),
            'organization_id' => $org2->id,
            'role' => 'organization_admin',
            'email_verified_at' => now(),
        ]);

        // Create a customer for Organization 2
        $customer3 = Customer::create([
            'organization_id' => $org2->id,
            'customer_id' => 'CUST-' . strtoupper(Str::random(8)),
            'company_name' => 'Retail Chain Inc',
            'address' => '555 Commerce Street',
            'city' => 'Chicago',
            'state' => 'IL',
            'postal_code' => '60601',
            'country' => 'USA',
            'contact_person' => 'David Lee',
            'contact_email' => 'david@retailchain.com',
            'contact_phone' => '+1-555-0401',
            'is_active' => true,
        ]);

        // Create equipment for Organization 2 customer
        $equipment4 = Equipment::create([
            'organization_id' => $org2->id,
            'customer_id' => $customer3->id,
            'equipment_type' => 'UPS System',
            'manufacturer' => 'APC',
            'model' => 'UPS-5000',
            'serial_number' => 'SN-22222-2023',
            'location' => 'Server Room',
            'description' => '5000 VA Uninterruptible Power Supply',
            'installation_date' => '2023-05-12',
            'status' => 'active',
        ]);

        Inspection::create([
            'organization_id' => $org2->id,
            'customer_id' => $customer3->id,
            'equipment_id' => $equipment4->id,
            'inspector_id' => $org2Admin->id,
            'inspection_type' => 'Initial Inspection',
            'inspection_date' => now()->subDays(5),
            'inspection_time' => '11:00:00',
            'result' => 'Pass - New installation verified',
            'notes' => 'All systems operational. Schedule quarterly inspections.',
            'status' => 'completed',
        ]);
    }
}
