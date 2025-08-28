import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';

export default function CreateOrganization() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        slug: '',
    });

    const [isSlugManual, setIsSlugManual] = React.useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('organizations.store'));
    };

    const handleNameChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const name = e.target.value;
        const newData = { ...data, name };
        
        // Auto-generate slug only if not manually edited
        if (!isSlugManual) {
            newData.slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        }
        
        setData(newData);
    };

    const handleSlugChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setIsSlugManual(true);
        setData('slug', e.target.value);
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Create Organization
                </h2>
            }
        >
            <Head title="Create Organization" />

            <div className="py-12">
                <div className="mx-auto max-w-2xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="name" value="Organization Name" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        value={data.name}
                                        onChange={handleNameChange}
                                        className="mt-1 block w-full"
                                        required
                                    />
                                    <InputError message={errors.name} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="slug" value="URL Slug" />
                                    <TextInput
                                        id="slug"
                                        type="text"
                                        value={data.slug}
                                        onChange={handleSlugChange}
                                        className="mt-1 block w-full"
                                        placeholder="Auto-generated from organization name"
                                        required
                                    />
                                    <p className="mt-1 text-sm text-gray-600">
                                        {isSlugManual 
                                            ? "Custom slug - this will be used in URLs and must be unique."
                                            : "Auto-generated from organization name. You can edit this if needed."
                                        }
                                    </p>
                                    <InputError message={errors.slug} className="mt-2" />
                                </div>

                                <div className="flex items-center justify-end space-x-4">
                                    <SecondaryButton
                                        type="button"
                                        onClick={() => window.history.back()}
                                    >
                                        Cancel
                                    </SecondaryButton>
                                    <PrimaryButton type="submit" disabled={processing}>
                                        {processing ? 'Creating...' : 'Create Organization'}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}