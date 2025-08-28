import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface Contact {
    id?: string;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    avatar_path: string | null;
    avatar_url: string | null;
}

interface ContactFormProps {
    contact?: Contact;
    onSuccess?: () => void;
}

export default function ContactForm({ contact, onSuccess }: ContactFormProps) {
    const isEditing = !!contact;
    
    const { data, setData, post, put, processing, errors, reset } = useForm({
        first_name: contact?.first_name || '',
        last_name: contact?.last_name || '',
        email: contact?.email || '',
        phone: contact?.phone || '',
        avatar: null as File | null,
        remove_avatar: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('first_name', data.first_name);
        formData.append('last_name', data.last_name);
        if (data.email) formData.append('email', data.email);
        if (data.phone) formData.append('phone', data.phone);
        if (data.avatar) formData.append('avatar', data.avatar);
        if (isEditing && data.remove_avatar) formData.append('remove_avatar', '1');

        const options = {
            forceFormData: true,
            onSuccess: () => {
                if (!isEditing) {
                    reset();
                }
                onSuccess?.();
            },
            onError: (errors: any) => {
                // The backend handles duplicate detection and redirects automatically
                // for web requests, so we don't need special handling here
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
                <Label htmlFor="first_name" className="text-black">
                    First Name *
                </Label>
                <Input
                    id="first_name"
                    type="text"
                    value={data.first_name}
                    onChange={(e) => setData('first_name', e.target.value)}
                    className="mt-1 border-gray-300 text-black"
                    required
                />
                {errors.first_name && (
                    <p className="mt-1 text-sm text-red-600">{errors.first_name}</p>
                )}
            </div>

            {/* Last Name */}
            <div>
                <Label htmlFor="last_name" className="text-black">
                    Last Name *
                </Label>
                <Input
                    id="last_name"
                    type="text"
                    value={data.last_name}
                    onChange={(e) => setData('last_name', e.target.value)}
                    className="mt-1 border-gray-300 text-black"
                    required
                />
                {errors.last_name && (
                    <p className="mt-1 text-sm text-red-600">{errors.last_name}</p>
                )}
            </div>

            {/* Email */}
            <div>
                <Label htmlFor="email" className="text-black">
                    Email
                </Label>
                <Input
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    className="mt-1 border-gray-300 text-black"
                />
                {errors.email && (
                    <p className="mt-1 text-sm text-red-600">{errors.email}</p>
                )}
            </div>

            {/* Phone */}
            <div>
                <Label htmlFor="phone" className="text-black">
                    Phone
                </Label>
                <Input
                    id="phone"
                    type="tel"
                    value={data.phone}
                    onChange={(e) => setData('phone', e.target.value)}
                    className="mt-1 border-gray-300 text-black"
                />
                {errors.phone && (
                    <p className="mt-1 text-sm text-red-600">{errors.phone}</p>
                )}
            </div>

            {/* Avatar */}
            <div>
                <Label htmlFor="avatar" className="text-black">
                    Avatar
                </Label>
                
                {/* Current Avatar Display */}
                {isEditing && contact?.avatar_url && !data.remove_avatar && (
                    <div className="mt-2 mb-2">
                        <img
                            src={contact.avatar_url}
                            alt="Current avatar"
                            className="w-16 h-16 rounded-full object-cover border border-gray-300"
                        />
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={removeAvatar}
                            className="mt-2 border-gray-300 text-black hover:bg-gray-100"
                        >
                            Remove Avatar
                        </Button>
                    </div>
                )}

                {/* File Input */}
                <Input
                    id="avatar"
                    type="file"
                    accept="image/jpeg,image/png,image/jpg,image/gif"
                    onChange={handleAvatarChange}
                    className="mt-1 border-gray-300 text-black"
                />
                
                {errors.avatar && (
                    <p className="mt-1 text-sm text-red-600">{errors.avatar}</p>
                )}
                
                <p className="mt-1 text-sm text-gray-500">
                    JPEG, PNG, JPG or GIF. Max 2MB.
                </p>
            </div>

            {/* Submit Button */}
            <div className="flex gap-2">
                <Button
                    type="submit"
                    disabled={processing}
                    className="bg-black text-white hover:bg-gray-800 disabled:bg-gray-400"
                >
                    {processing ? 'Saving...' : (isEditing ? 'Update Contact' : 'Create Contact')}
                </Button>
                
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => window.history.back()}
                    className="border-gray-300 text-black hover:bg-gray-100"
                >
                    Cancel
                </Button>
            </div>
        </form>
    );
}