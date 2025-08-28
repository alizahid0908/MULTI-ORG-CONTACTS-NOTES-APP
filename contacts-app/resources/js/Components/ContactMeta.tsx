import { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Trash2, Plus } from 'lucide-react';

interface ContactMeta {
    id: string;
    key: string;
    value: string;
}

interface Contact {
    id: string;
    meta: ContactMeta[];
}

interface ContactMetaProps {
    contact: Contact;
    canManageMeta: boolean;
}

export default function ContactMeta({ contact, canManageMeta }: ContactMetaProps) {
    const [showForm, setShowForm] = useState(false);
    
    const { data, setData, post, processing, errors, reset } = useForm({
        key: '',
        value: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        post(`/contacts/${contact.id}/meta`, {
            onSuccess: () => {
                reset();
                setShowForm(false);
            },
        });
    };

    const handleDelete = (metaId: string) => {
        if (confirm('Are you sure you want to delete this custom field?')) {
            router.delete(`/contacts/${contact.id}/meta/${metaId}`);
        }
    };

    const canAddMore = contact.meta.length < 5;

    return (
        <div className="bg-white border border-gray-300 rounded-lg p-6">
            <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold text-black">Custom Fields</h3>
                {canManageMeta && canAddMore && (
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setShowForm(!showForm)}
                        className="border-gray-300 text-black hover:bg-gray-100"
                    >
                        <Plus className="w-4 h-4 mr-1" />
                        Add Field
                    </Button>
                )}
            </div>

            {/* Existing Meta Fields */}
            {contact.meta.length > 0 ? (
                <div className="space-y-3 mb-4">
                    {contact.meta.map((meta) => (
                        <div key={meta.id} className="flex items-start justify-between p-3 border border-gray-200 rounded-lg">
                            <div className="flex-1">
                                <div className="font-medium text-black text-sm">{meta.key}</div>
                                <div className="text-gray-600 text-sm mt-1">{meta.value}</div>
                            </div>
                            {canManageMeta && (
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => handleDelete(meta.id)}
                                    className="text-red-600 hover:text-red-700 hover:bg-red-50 ml-2"
                                >
                                    <Trash2 className="w-4 h-4" />
                                </Button>
                            )}
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-gray-500 text-sm mb-4">No custom fields added yet.</p>
            )}

            {/* Add New Field Form */}
            {showForm && canManageMeta && (
                <form onSubmit={handleSubmit} className="space-y-4 border-t border-gray-200 pt-4">
                    <div>
                        <Label htmlFor="key" className="text-black">Field Name</Label>
                        <Input
                            id="key"
                            type="text"
                            value={data.key}
                            onChange={(e) => setData('key', e.target.value)}
                            placeholder="e.g., Department, Title, Notes"
                            className="border-gray-300 focus:border-black focus:ring-black"
                            maxLength={100}
                        />
                        {errors.key && (
                            <Alert className="mt-2 border-red-300 bg-red-50">
                                <AlertDescription className="text-red-800 text-sm">
                                    {errors.key}
                                </AlertDescription>
                            </Alert>
                        )}
                    </div>

                    <div>
                        <Label htmlFor="value" className="text-black">Field Value</Label>
                        <Textarea
                            id="value"
                            value={data.value}
                            onChange={(e) => setData('value', e.target.value)}
                            placeholder="Enter the field value"
                            className="border-gray-300 focus:border-black focus:ring-black"
                            rows={3}
                            maxLength={1000}
                        />
                        {errors.value && (
                            <Alert className="mt-2 border-red-300 bg-red-50">
                                <AlertDescription className="text-red-800 text-sm">
                                    {errors.value}
                                </AlertDescription>
                            </Alert>
                        )}
                    </div>

                    <div className="flex gap-2">
                        <Button
                            type="submit"
                            disabled={processing}
                            className="bg-black text-white hover:bg-gray-800"
                        >
                            {processing ? 'Adding...' : 'Add Field'}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => {
                                setShowForm(false);
                                reset();
                            }}
                            className="border-gray-300 text-black hover:bg-gray-100"
                        >
                            Cancel
                        </Button>
                    </div>
                </form>
            )}

            {!canAddMore && canManageMeta && (
                <Alert className="border-yellow-300 bg-yellow-50">
                    <AlertDescription className="text-yellow-800 text-sm">
                        Maximum of 5 custom fields allowed per contact.
                    </AlertDescription>
                </Alert>
            )}
        </div>
    );
}