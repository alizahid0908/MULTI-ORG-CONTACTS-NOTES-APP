import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface Contact {
    id: string;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    avatar_path: string | null;
    avatar_url: string | null;
    full_name: string;
    created_at: string;
    creator: {
        name: string;
    };
}

interface PaginatedContacts {
    data: Contact[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

interface PageProps {
    contacts: PaginatedContacts;
    search: string | null;
    can: {
        create: boolean;
    };
    flash: {
        success?: string;
        error?: string;
    };
}

export default function ContactsIndex() {
    const { contacts, search, can, flash } = usePage<PageProps>().props;
    const [searchTerm, setSearchTerm] = useState(search || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/contacts', { search: searchTerm }, {
            preserveState: true,
            replace: true,
        });
    };

    const clearSearch = () => {
        setSearchTerm('');
        router.get('/contacts', {}, {
            preserveState: true,
            replace: true,
        });
    };

    return (
        <>
            <Head title="Contacts" />
            
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-bold text-black">Contacts</h1>
                    {can.create && (
                        <Link href="/contacts/create">
                            <Button className="bg-black text-white hover:bg-gray-800">
                                Add Contact
                            </Button>
                        </Link>
                    )}
                </div>

                {/* Flash Messages */}
                {flash.success && (
                    <Alert className="mb-6 border-green-300 bg-green-50">
                        <AlertDescription className="text-green-800">
                            {flash.success}
                        </AlertDescription>
                    </Alert>
                )}
                
                {flash.error && (
                    <Alert className="mb-6 border-red-300 bg-red-50">
                        <AlertDescription className="text-red-800">
                            {flash.error}
                        </AlertDescription>
                    </Alert>
                )}

                {/* Search */}
                <form onSubmit={handleSearch} className="mb-6">
                    <div className="flex gap-2 max-w-md">
                        <Input
                            type="text"
                            placeholder="Search contacts by name or email..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="border-gray-300 text-black"
                        />
                        <Button 
                            type="submit" 
                            className="bg-black text-white hover:bg-gray-800"
                        >
                            Search
                        </Button>
                        {search && (
                            <Button 
                                type="button" 
                                variant="outline"
                                onClick={clearSearch}
                                className="border-gray-300 text-black hover:bg-gray-100"
                            >
                                Clear
                            </Button>
                        )}
                    </div>
                </form>

                {/* Contacts Table */}
                {contacts.data.length > 0 ? (
                    <div className="bg-white border border-gray-300 rounded-lg">
                        <Table>
                            <TableHeader>
                                <TableRow className="border-gray-300">
                                    <TableHead className="text-black font-medium">Avatar</TableHead>
                                    <TableHead className="text-black font-medium">Name</TableHead>
                                    <TableHead className="text-black font-medium">Email</TableHead>
                                    <TableHead className="text-black font-medium">Phone</TableHead>
                                    <TableHead className="text-black font-medium">Created</TableHead>
                                    <TableHead className="text-black font-medium">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {contacts.data.map((contact) => (
                                    <TableRow 
                                        key={contact.id} 
                                        className="border-gray-300 hover:bg-gray-50"
                                    >
                                        <TableCell>
                                            {contact.avatar_url ? (
                                                <img
                                                    src={contact.avatar_url}
                                                    alt={contact.full_name}
                                                    className="w-8 h-8 rounded-full object-cover border border-gray-300"
                                                />
                                            ) : (
                                                <div className="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                                                    <span className="text-xs text-gray-600">
                                                        {contact.first_name.charAt(0)}{contact.last_name.charAt(0)}
                                                    </span>
                                                </div>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-black font-medium">
                                            <Link 
                                                href={`/contacts/${contact.id}`}
                                                className="hover:underline"
                                            >
                                                {contact.full_name}
                                            </Link>
                                        </TableCell>
                                        <TableCell className="text-gray-600">
                                            {contact.email || '-'}
                                        </TableCell>
                                        <TableCell className="text-gray-600">
                                            {contact.phone || '-'}
                                        </TableCell>
                                        <TableCell className="text-gray-600 text-sm">
                                            {new Date(contact.created_at).toLocaleDateString()}
                                        </TableCell>
                                        <TableCell>
                                            <Link href={`/contacts/${contact.id}`}>
                                                <Button 
                                                    variant="outline" 
                                                    size="sm"
                                                    className="border-gray-300 text-black hover:bg-gray-100"
                                                >
                                                    View
                                                </Button>
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>

                        {/* Pagination */}
                        {contacts.last_page > 1 && (
                            <div className="flex justify-center items-center gap-2 p-4 border-t border-gray-300">
                                {contacts.links.map((link, index) => (
                                    <Button
                                        key={index}
                                        variant={link.active ? "default" : "outline"}
                                        size="sm"
                                        disabled={!link.url}
                                        onClick={() => link.url && router.get(link.url)}
                                        className={link.active 
                                            ? "bg-black text-white" 
                                            : "border-gray-300 text-black hover:bg-gray-100"
                                        }
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                ) : (
                    <div className="text-center py-12 bg-white border border-gray-300 rounded-lg">
                        <p className="text-gray-500 text-lg mb-4">
                            {search ? 'No contacts found matching your search.' : 'No contacts in this organization.'}
                        </p>
                        {can.create && !search && (
                            <Link href="/contacts/create">
                                <Button className="bg-black text-white hover:bg-gray-800">
                                    Add Your First Contact
                                </Button>
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </>
    );
}