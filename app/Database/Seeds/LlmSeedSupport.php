<?php

namespace App\Database\Seeds;

/**
 * Shared pools and helpers for LLM test-data seeders.
 */
trait LlmSeedSupport
{
    protected string $llmMarker = 'LLM_DEMO_DATA';

    /** Target row counts — adjust here if requirements change. */
    protected int $llmStaffTarget = 2000;
    protected int $llmVisitorApprovedTarget = 1985; // + curated demo approved ≈ 2000 on visitor pass list
    protected int $llmVisitorPendingTarget = 80;
    protected int $llmVisitorSubmittedTarget = 100;
    protected int $llmVisitorRejectedTarget = 50;
    protected int $llmBlacklistTarget = 100;
    protected int $llmSecurityAlertTarget = 150;
    protected int $llmVisitorCardTarget = 200;
    protected int $llmBatchSize = 250;

    protected function llmUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    protected function llmFirstNames(): array
    {
        return [
            'Ahmad', 'Nurul', 'Ravi', 'Mei Ling', 'Sarah', 'Hassan', 'Priya', 'Wei Jie', 'Fatimah', 'Raj',
            'Siti', 'Kumar', 'Tan', 'Lim', 'Ong', 'Nair', 'Bakar', 'Chong', 'Aziz', 'Wong',
            'Ismail', 'Devi', 'Hafiz', 'Yusof', 'Anand', 'Liew', 'Zainab', 'Arif', 'Grace', 'Daniel',
            'Amirah', 'Vikram', 'Joanne', 'Syafiq', 'Helen', 'Marcus', 'Aina', 'Farid', 'Cynthia', 'Irfan',
        ];
    }

    protected function llmLastNames(): array
    {
        return [
            'Abdullah', 'Rahman', 'Lee', 'Tan', 'Kumar', 'Wong', 'Lim', 'Nair', 'Bakar', 'Ong',
            'Hassan', 'Devi', 'Ismail', 'Chong', 'Aziz', 'Yusof', 'Menon', 'Goh', 'Salleh', 'Teo',
            'Pillai', 'Ng', 'Ibrahim', 'Chan', 'Rosli', 'Subramaniam', 'Ho', 'Zainal', 'Krishnan', 'Low',
        ];
    }

    protected function llmVisitorCompanies(): array
    {
        return [
            'LLM Demo Logistics', 'LLM Demo Engineering', 'LLM Demo Catering',
            'Acme Industrial Supplies', 'Nova Precision Parts', 'Global Freight Solutions',
            'Metro Catering Services', 'BrightStar IT Consulting', 'Pacific Tooling Sdn Bhd',
            'Summit Safety Systems', 'Orion Semiconductor', 'GreenField Contractors',
            'Vertex Automation', 'Harbour Logistics', 'Skyline Facilities',
        ];
    }

    protected function llmVisitLocations(): array
    {
        return [
            '1. VISITOR STAIRCASE',
            '3. HR & ADMIN ENTRANCE',
            '4. VISITOR ENTRANCE TO PRODUCTION',
            '6. CMM ROOM',
            '8. POLISHING ROOM',
            '10. 10K CLEANROOM',
            '12. ROBOTIC WELDING ROOM',
            '13.TURNSTILE',
            'Main Office - Reception Area',
            'North Branch - Lobby Entrance',
            'Warehouse A - Loading Bay',
            'Server Room - Level 3',
            'QA Room - Inspection Bay',
            'Packaging Area - Gate 2',
        ];
    }

    protected function llmVisitReasons(): array
    {
        return [
            'Delivery coordination', 'Vendor briefing', 'Menu review', 'Inventory audit',
            'Network maintenance', 'Courier handover', 'Equipment calibration', 'Supplier meeting',
            'Site survey', 'Contract discussion', 'Safety audit', 'Machine commissioning',
            'Training session', 'Quality inspection', 'Parts delivery', 'Software deployment',
            'Facilities walkthrough', 'Interview', 'Customer buyoff', 'Tool calibration',
        ];
    }

    protected function llmDepartments(): array
    {
        return [
            'Engineering', 'Operations', 'HR', 'Security', 'QA', 'Production',
            'IT', 'Facilities', 'Finance', 'Logistics', 'Procurement', 'Admin',
        ];
    }

    protected function llmDesignations(): array
    {
        return [
            'Engineer', 'Supervisor', 'Manager', 'Executive', 'Technician', 'Analyst',
            'Coordinator', 'Specialist', 'Lead', 'Officer', 'Assistant Manager', 'Director',
        ];
    }

    protected function llmTableHasColumn($db, string $table, string $column): bool
    {
        if (! $db->tableExists($table)) {
            return false;
        }

        return $db->fieldExists($column, $table);
    }
}
