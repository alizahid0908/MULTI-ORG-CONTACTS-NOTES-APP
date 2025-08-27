import { router } from '@inertiajs/react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface Organization {
    id: string;
    name: string;
    slug: string;
    owner_user_id: string;
}

interface OrgSwitcherProps {
    organizations: Organization[];
    currentOrganization: Organization | null;
    className?: string;
}

export default function OrgSwitcher({
    organizations,
    currentOrganization,
    className = '',
}: OrgSwitcherProps) {
    const handleOrgChange = (organizationId: string) => {
        // POST to /organizations/switch with organization_id
        router.post('/organizations/switch', {
            organization_id: organizationId,
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Reload the page to reflect the organization change
                router.reload();
            },
            onError: (errors) => {
                console.error('Failed to switch organization:', errors);
            },
        });
    };

    if (!organizations || organizations.length === 0) {
        return (
            <div className={`text-sm text-gray-500 ${className}`}>
                No organizations available
            </div>
        );
    }

    return (
        <div className={className}>
            <Select
                value={currentOrganization?.id || ''}
                onValueChange={handleOrgChange}
            >
                <SelectTrigger className="w-48 bg-white border-gray-300 text-black">
                    <SelectValue 
                        placeholder="Select organization"
                        className="text-black"
                    />
                </SelectTrigger>
                <SelectContent className="bg-white border-gray-300">
                    {organizations.map((org) => (
                        <SelectItem
                            key={org.id}
                            value={org.id}
                            className="text-black hover:bg-gray-100 focus:bg-gray-100"
                        >
                            <div className="flex flex-col">
                                <span className="font-medium">{org.name}</span>
                                <span className="text-xs text-gray-500">
                                    /{org.slug}
                                </span>
                            </div>
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}