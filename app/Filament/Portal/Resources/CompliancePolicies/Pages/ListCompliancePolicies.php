<?php

namespace App\Filament\Portal\Resources\CompliancePolicies\Pages;

use App\Filament\Portal\Resources\CompliancePolicies\CompliancePolicyResource;
use App\Models\CompliancePolicy;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListCompliancePolicies extends ListRecords
{
    protected static string $resource = CompliancePolicyResource::class;

    protected string $view = 'filament.portal.resources.compliance-policies.list-compliance-policies';

    public ?string $search = '';

    public string $selectedCategory = 'all';

    public ?int $activePolicyId = null;

    public function setCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function openDetail(int $id): void
    {
        $this->activePolicyId = $id;
    }

    public function closeDetail(): void
    {
        $this->activePolicyId = null;
    }

    public function getActivePolicy(): ?CompliancePolicy
    {
        if (! $this->activePolicyId) {
            return null;
        }

        return CompliancePolicy::accessibleBy()->find($this->activePolicyId);
    }

    public function getPoliciesProperty(): Collection
    {
        $query = CompliancePolicy::accessibleBy()
            ->latest('effective_date');

        if ($this->selectedCategory !== 'all') {
            $query->where('category', $this->selectedCategory);
        }

        if (filled($this->search)) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('document_number', 'like', $search)
                    ->orWhere('summary', 'like', $search);
            });
        }

        return $query->get();
    }

    public function getCategoryCountsProperty(): array
    {
        $base = CompliancePolicy::accessibleBy();

        return [
            'all' => (clone $base)->count(),
            'apu_ppt' => (clone $base)->where('category', 'apu_ppt')->count(),
            'code_of_conduct' => (clone $base)->where('category', 'code_of_conduct')->count(),
            'sop_operation' => (clone $base)->where('category', 'sop_operation')->count(),
            'risk_management' => (clone $base)->where('category', 'risk_management')->count(),
            'company_regulation' => (clone $base)->where('category', 'company_regulation')->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
