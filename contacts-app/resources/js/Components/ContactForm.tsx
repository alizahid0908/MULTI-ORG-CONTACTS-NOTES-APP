import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';

interface Contact {
    id?: string;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    avatar_path: string | null;
    avatar_url: string | null;
}

interface InitialData {
    first_name?: string;
    last_name?: string;
    email?: string | null;
    phone?: string | null;
}

interface ContactFormProps {
    contact?: Contact;
    initialData?: InitialData;
    onSuccess?: () => void;
}

export default function ContactForm({ contact, initialData, onSuccess }: ContactFormProps) {
    const isEditing = !!contact;
    
    // Use initialData for duplication, contact for editing, or empty for new
    const formData = initialData || contact || {};
    
    const { data, setData, post, put, processing, errors, reset } = useForm({
        first_name: formData.first_name || '',
        last_name: formData.last_name || '',
        email: formData.email || '',
        phone: formData.phone || '',
        avatar: null as File | null,
        remove_avatar: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        console.log('Form data before submission:', data);

        const options = {
            onSuccess: () => {
                if (!isEditing) {
                    reset();
                }
                onSuccess?.();
            },
            onError: (errors: any) => {
                console.log('Form submission errors:', errors);
            },
        };

        if (isEditing && contact) {
            put(`/contacts/${contact.id}`, options);
        } else {
            post('/contacts', options);
        }
    };

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] || null;
        console.log('Avatar file selected:', file);
        setData('avatar', file);
        if (file) {
            setData('remove_avatar', false);
        }
    };

    const removeAvatar = () => {
        setData('avatar', null);
        setData('remove_avatar', true);
        // Clear file input
        const fileInput = document.getElementById('avatar') as HTMLInputElement;
        if (fileInput) fileInput.value = '';
    };

    return (
        <form onSubmit={submit} className="space-y-6 max-w-md">
            {/* First Name */}
            <div>
                <InputLabel htmlFor="first_name" value="First Name *" />
                <TextInput
                    id="first_name"
                    type="text"
                    value={data.first_name}
                    onChange={(e) => setData('first_name', e.target.value)}
                    className="mt-1 block w-full"
                    required
                />
                <InputError message={errors.first_name} className="mt-2" />
            </div>

            {/* Last Name */}
            <div>
                <InputLabel htmlFor="last_name" value="Last Name *" />
                <TextInput
                    id="last_name"
                    type="text"
                    value={data.last_name}
                    onChange={(e) => setData('last_name', e.target.value)}
                    className="mt-1 block w-full"
                    required
                />
                <InputError message={errors.last_name} className="mt-2" />
            </div>

            {/* Email */}
            <div>
                <InputLabel htmlFor="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    className="mt-1 block w-full"
                />
                <InputError message={errors.email} className="mt-2" />
            </div>

            {/* Phone */}
            <div>
                <InputLabel htmlFor="phone" value="Phone" />
                <TextInput
                    id="phone"
                    type="tel"
                    value={data.phone}
                    onChange={(e) => setData('phone', e.target.value)}
                    className="mt-1 block w-full"
                />
                <InputError message={errors.phone} className="mt-2" />
            </div>

            {/* Avatar */}
            <div>
                <InputLabel htmlFor="avatar" value="Avatar" />
                
                {/* Current Avatar Display */}
                {isEditing && contact?.avatar_url && !data.remove_avatar && (
                    <div className="mt-2 mb-2">
                        <img
                            src={contact.avatar_url}
                            alt="Current avatar"
                            className="w-16 h-16 rounded-full object-cover border border-gray-300"
                        />
                        <SecondaryButton
                            type="button"
                            onClick={removeAvatar}
                            className="mt-2"
                        >
                            Remove Avatar
                        </SecondaryButton>
                    </div>
                )}

                {/* File Input */}
                <input
                    id="avatar"
                    type="file"
                    accept="image/jpeg,image/png,image/jpg,image/gif"
                    onChange={handleAvatarChange}
                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100"
                />
                
                <InputError message={errors.avatar} className="mt-2" />
                
                <p className="mt-1 text-sm text-gray-500">
                    JPEG, PNG, JPG or GIF. Max 2MB.
                </p>
            </div>

            {/* Submit Button */}
            <div className="flex gap-2">
                <PrimaryButton
                    type="submit"
                    disabled={processing}
                >
                    {processing ? 'Saving...' : (isEditing ? 'Update Contact' : 'Create Contact')}
                </PrimaryButton>
                
                <SecondaryButton
                    type="button"
                    onClick={() => window.history.back()}
                >
                    Cancel
                </SecondaryButton>
            </div>
        </form>
    );
}