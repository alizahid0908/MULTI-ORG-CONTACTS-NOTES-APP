import { Head, Link, usePage } from '@inertiajs/react';
import ContactForm from '@/Components/ContactForm';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface PageProps {
    flash: {
        success?: string;
        error?: string;
    };
}

export default function ContactCreate() {
    const { flash } = usePage<PageProps>().props;
    return (
        <>
            <Head title="Create Contact" />
            
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
                        <span className="text-black">Create</span>
                    </div>
                    <h1 className="text-2xl font-bold text-black">Create Contact</h1>
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