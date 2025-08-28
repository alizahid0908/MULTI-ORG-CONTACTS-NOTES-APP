import { Head, Link, router, usePage } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import ContactNotes from '@/Components/ContactNotes';
import ContactMeta from '@/Components/ContactMeta';

interface User {
    id: string;
    name: string;
    email: string;
}

interface ContactNote {
    id: string;
    body: string;
    created_at: string;
    user: User;
}

interface ContactMeta {
    id: string;
    key: string;
    value: string;
}

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
    updated_at: string;
    creator: User;
    updater: User;
    notes: ContactNote[];
    meta: ContactMeta[];
}

interface PageProps {
    contact: Contact;
    can: {
        update: boolean;
        delete: boolean;
        duplicate: boolean;
        createNotes: boolean;
        manageMeta: boolean;
    };
    flash: {
        success?: string;
        error?: string;
    };
}

export default function ContactShow() {
    const { contact, can, flash } = usePage<PageProps>().props;

    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
            router.delete(`/contacts/${contact.id}`);
        }
    };

    const handleDuplicate = () => {
        router.visit(`/contacts/${contact.id}/duplicate`);
    };

    return (
        <>
            <Head title={contact.full_name} />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="mb-6">
                    <div className="flex items-center gap-2 mb-2">
                        <Link
                            href="/contacts"
                            className="text-gray-600 hover:text-black"
                        >
                            Contacts
                        </Link>
                        <span className="text-gray-400">/</span>
                        <span className="text-black">{contact.full_name}</span>
                    </div>

                    <div className="flex justify-between items-start">
                        <h1 className="text-2xl font-bold text-black">{contact.full_name}</h1>

                        <div className="flex gap-2">
                            {can.update && (
                                <Link href={`/contacts/${contact.id}/edit`}>
                                    <Button
                                        variant="outline"
                                        className="border-gray-300 text-black hover:bg-gray-100"
                                    >
                                        Edit
                                    </Button>
                                </Link>
                            )}

                            {can.duplicate && (
                                <Button
                                    variant="outline"
                                    onClick={handleDuplicate}
                                    className="border-gray-300 text-black hover:bg-gray-100"
                                >
                                    Duplicate
                                </Button>
                            )}

                            {can.delete && (
                                <Button
                                    variant="outline"
                                    onClick={handleDelete}
                                    className="border-red-300 text-red-600 hover:bg-red-50"
                                >
                                    Delete
                                </Button>
                            )}
                        </div>
                    </div>
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

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Contact Information */}
                    <div className="lg:col-span-2">
                        <div className="bg-white border border-gray-300 rounded-lg p-6">
                            <h2 className="text-lg font-semibold text-black mb-4">Contact Information</h2>

                            <div className="flex items-start gap-4 mb-6">
                                {contact.avatar_url ? (
                                    <img
                                        src={contact.avatar_url}
                                        alt={contact.full_name}
                                        className="w-20 h-20 rounded-full object-cover border border-gray-300"
                                    />
                                ) : (
                                    <div className="w-20 h-20 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                                        <span className="text-xl text-gray-600">
                                            {contact.first_name.charAt(0)}{contact.last_name.charAt(0)}
                                        </span>
                                    </div>
                                )}

                                <div className="flex-1">
                                    <h3 className="text-xl font-semibold text-black">{contact.full_name}</h3>
                                    {contact.email && (
                                        <p className="text-gray-600">
                                            <a
                                                href={`mailto:${contact.email}`}
                                                className="hover:underline"
                                            >
                                                {contact.email}
                                            </a>
                                        </p>
                                    )}
                                    {contact.phone && (
                                        <p className="text-gray-600">
                                            <a
                                                href={`tel:${contact.phone}`}
                                                className="hover:underline"
                                            >
                                                {contact.phone}
                                            </a>
                                        </p>
                                    )}
                                </div>
                            </div>



                            {/* Audit Information */}
                            <div className="text-sm text-gray-500 space-y-1">
                                <p>Created by {contact.creator.name} on {new Date(contact.created_at).toLocaleDateString()}</p>
                                <p>Last updated by {contact.updater.name} on {new Date(contact.updated_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Custom Fields */}
                        <ContactMeta
                            contact={contact}
                            canManageMeta={can.manageMeta}
                        />

                        {/* Notes Section */}
                        <ContactNotes
                            contact={contact}
                            canCreateNotes={can.createNotes}
                        />
                    </div>
                </div>
            </div>
        </>
    );
}