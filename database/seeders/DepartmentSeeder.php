<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            "Human Resources (HR)", "Operations (OPS)", "Sales", "Marketing", "Finance", "Accounting",
            "Customer Support / Customer Service", "Information Technology (IT)", "Software Development",
            "Web Development", "Mobile App Development", "Cyber Security", "Network & System Administration",
            "Data Analysis", "Data Science", "Artificial Intelligence", "Project Management", "Product Management",
            "Quality Assurance (QA)", "Testing", "UI/UX Design", "Graphic Design", "Motion Graphics",
            "Video Editing", "Content Creation", "Social Media Management", "Digital Marketing", "SEO / SEM",
            "Business Development", "Procurement", "Supply Chain", "Logistics", "Warehouse Management",
            "Public Relations (PR)", "Legal Affairs", "Compliance", "Risk Management", "Internal Audit",
            "Research & Development (R&D)", "Engineering", "Electrical Engineering", "Mechanical Engineering",
            "Civil Engineering", "Architecture", "Technical Support", "Call Center", "Training & Development",
        ];

        foreach ($departments as $name) {
            Department::create(['name' => $name]);
        }
    }
}
