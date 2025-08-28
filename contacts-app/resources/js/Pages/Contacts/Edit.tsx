import { Head, Link, usePage } from '@inertiajs/react';
import ContactForm from '@/Components/ContactForm';
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
}

interface PageProps {
    contact: Contact;
    flash: {
        success?: string;
        error?: string;
    };
}

export default function ContactEdit() {
    const { contact, flash } = usePage<PageProps>().props;

    return (
        <>
            <Head title={`Edit ${contact.full_name}`} />
            
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
                        <Link 
                            href={`/contacts/${contact.id}`}
                            className="text-gray-600 hover:text-black"
                        >
                            {contact.full_name}
                        </Link>
                        <span className="text-gray-400">/</span>
                        <span className="text-black">Edit</span>
                    </div>
                    <h1 className="text-2xl font-bold text-black">Edit {contact.full_name}</h1>
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

                {/* Form */}
                <div className="bg-white border border-gray-300 rounded-lg p-6">
                    <ContactForm 
                        contact={contact}
                        onSuccess={() => {
                            // Success handling is done in the form component
                            // via redirect to contact show page
                        }}
                    />
                </div>
            </div>
        </>
    );
}