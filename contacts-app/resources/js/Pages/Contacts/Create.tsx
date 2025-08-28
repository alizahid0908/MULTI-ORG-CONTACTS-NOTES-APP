import { Head, Link, usePage } from '@inertiajs/react';
import ContactForm from '@/Components/ContactForm';
import { Alert, AlertDescription } from '@/Components/ui/alert';

interface Contact {
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
}

interface PageProps {
    flash: {
        success?: string;
        error?: string;
    };
    duplicateFrom?: string;
    initialData?: Contact;
}

export default function ContactCreate() {
    const { flash, duplicateFrom, initialData } = usePage<PageProps>().props;
    
    const pageTitle = duplicateFrom ? `Duplicate Contact from ${duplicateFrom}` : 'Create Contact';
    
    return (
        <>
            <Head title={pageTitle} />
            
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
                        <span className="text-black">{duplicateFrom ? 'Duplicate' : 'Create'}</span>
                    </div>
                    <h1 className="text-2xl font-bold text-black">{pageTitle}</h1>
                    {duplicateFrom && (
                        <p className="text-sm text-gray-600 mt-1">
                            Creating a copy of "{duplicateFrom}". Email has been cleared - please add a new one.
                        </p>
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

                {/* Form */}
                <div className="bg-white border border-gray-300 rounded-lg p-6">
                    <ContactForm 
                        initialData={initialData}
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