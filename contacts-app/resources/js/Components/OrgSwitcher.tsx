import { router } from '@inertiajs/react';
import Dropdown from '@/Components/Dropdown';

interface Organization {
    id: string;
    name: string;
    slug: string;
    owner_user_id: string;
}

interface OrgSwitcherProps {
    organizations: Organization[];
    CurrentOrganizationService: Organization | null;
    className?: string;
}

export default function OrgSwitcher({
    organizations,
    CurrentOrganizationService,
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

    const handleCreateNew = () => {
        router.visit('/organizations/create');
    };

    if (!organizations || organizations.length === 0) {
        return (
            <div className={`text-sm text-gray-500 ${className}`}>
                <button
                    onClick={handleCreateNew}
                    className="text-blue-600 hover:text-blue-800 underline"
                >
                    Create your first organization
                </button>
            </div>
        );
    }

    return (
        <div className={className}>
            <Dropdown>
                <Dropdown.Trigger>
                    <button
                        type="button"
                        className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <div className="flex flex-col items-start">
                            <span className="font-medium">
                                {CurrentOrganizationService?.name || 'Select Organization'}
                            </span>
                            {CurrentOrganizationService && (
                                <span className="text-xs text-gray-500">
                                    /{CurrentOrganizationService.slug}
                                </span>
                            )}
                        </div>
                        <svg
                            className="-mr-1 ml-2 h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fillRule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clipRule="evenodd"
                            />
                        </svg>
                    </button>
                </Dropdown.Trigger>

                <Dropdown.Content>
                    {organizations.map((org) => (
                        <button
                            key={org.id}
                            onClick={() => handleOrgChange(org.id)}
                            className={`block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 ${
                                CurrentOrganizationService?.id === org.id
                                    ? 'bg-gray-50 text-gray-900'
                                    : 'text-gray-700'
                            }`}
                        >
                            <div className="flex items-center">
                                <div className="flex flex-col">
                                    <span className="font-medium">{org.name}</span>
                                    <span className="text-xs text-gray-500">
                                        /{org.slug}
                                    </span>
                                </div>
                                {CurrentOrganizationService?.id === org.id && (
                                    <svg
                                        className="ml-auto h-4 w-4 text-green-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                )}
                            </div>
                        </button>
                    ))}
                    
                    <div className="border-t border-gray-100">
                        <button
                            onClick={handleCreateNew}
                            className="block w-full px-4 py-2 text-left text-sm text-blue-600 hover:bg-blue-50 hover:text-blue-800"
                        >
                            <div className="flex items-center">
                                <svg
                                    className="mr-2 h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                    />
                                </svg>
                                Create New Organization
                            </div>
                        </button>
                    </div>
                </Dropdown.Content>
            </Dropdown>
        </div>
    );
}