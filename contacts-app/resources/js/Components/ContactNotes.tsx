import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';

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

interface Contact {
    id: string;
    notes: ContactNote[];
}

interface ContactNotesProps {
    contact: Contact;
    canCreateNotes?: boolean;
}

interface PageProps {
    auth: {
        user: User;
    };
}

export default function ContactNotes({ contact, canCreateNotes = true }: ContactNotesProps) {
    const { auth } = usePage<PageProps>().props;
    const [isAddingNote, setIsAddingNote] = useState(false);
    const [noteBody, setNoteBody] = useState('');
    const [editingNoteId, setEditingNoteId] = useState<string | null>(null);
    const [editingBody, setEditingBody] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleAddNote = () => {
        if (!noteBody.trim()) return;

        setIsSubmitting(true);
        router.post(`/contacts/${contact.id}/notes`, {
            body: noteBody,
        }, {
            onSuccess: () => {
                setNoteBody('');
                setIsAddingNote(false);
            },
            onFinish: () => setIsSubmitting(false),
        });
    };

    const handleEditNote = (note: ContactNote) => {
        setEditingNoteId(note.id);
        setEditingBody(note.body);
    };

    const handleUpdateNote = (noteId: string) => {
        if (!editingBody.trim()) return;

        setIsSubmitting(true);
        router.put(`/contacts/${contact.id}/notes/${noteId}`, {
            body: editingBody,
        }, {
            onSuccess: () => {
                setEditingNoteId(null);
                setEditingBody('');
            },
            onFinish: () => setIsSubmitting(false),
        });
    };

    const handleDeleteNote = (noteId: string) => {
        if (confirm('Are you sure you want to delete this note?')) {
            router.delete(`/contacts/${contact.id}/notes/${noteId}`);
        }
    };

    const canEditNote = (note: ContactNote) => {
        return note.user.id === auth.user.id;
    };

    return (
        <div className="bg-white border border-gray-300 rounded-lg p-6">
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-lg font-semibold text-black">Notes</h2>
                {canCreateNotes && !isAddingNote && (
                    <Button 
                        onClick={() => setIsAddingNote(true)}
                        className="bg-black text-white hover:bg-gray-800"
                        size="sm"
                    >
                        Add Note
                    </Button>
                )}
            </div>

            {/* Add Note Form */}
            {isAddingNote && (
                <div className="mb-6 p-4 border border-gray-300 rounded-lg bg-gray-50">
                    <Textarea
                        value={noteBody}
                        onChange={(e) => setNoteBody(e.target.value)}
                        placeholder="Enter your note..."
                        className="mb-3 border-gray-300 focus:border-black focus:ring-black"
                        rows={3}
                    />
                    <div className="flex gap-2">
                        <Button 
                            onClick={handleAddNote}
                            disabled={!noteBody.trim() || isSubmitting}
                            className="bg-black text-white hover:bg-gray-800"
                            size="sm"
                        >
                            {isSubmitting ? 'Adding...' : 'Add Note'}
                        </Button>
                        <Button 
                            onClick={() => {
                                setIsAddingNote(false);
                                setNoteBody('');
                            }}
                            variant="outline"
                            className="border-gray-300 text-black hover:bg-gray-100"
                            size="sm"
                        >
                            Cancel
                        </Button>
                    </div>
                </div>
            )}

            {/* Notes List */}
            {contact.notes.length > 0 ? (
                <div className="space-y-4">
                    {contact.notes.map((note) => (
                        <div key={note.id} className="border-b border-gray-200 pb-4 last:border-b-0">
                            {editingNoteId === note.id ? (
                                <div className="space-y-3">
                                    <Textarea
                                        value={editingBody}
                                        onChange={(e) => setEditingBody(e.target.value)}
                                        className="border-gray-300 focus:border-black focus:ring-black"
                                        rows={3}
                                    />
                                    <div className="flex gap-2">
                                        <Button 
                                            onClick={() => handleUpdateNote(note.id)}
                                            disabled={!editingBody.trim() || isSubmitting}
                                            className="bg-black text-white hover:bg-gray-800"
                                            size="sm"
                                        >
                                            {isSubmitting ? 'Updating...' : 'Update'}
                                        </Button>
                                        <Button 
                                            onClick={() => {
                                                setEditingNoteId(null);
                                                setEditingBody('');
                                            }}
                                            variant="outline"
                                            className="border-gray-300 text-black hover:bg-gray-100"
                                            size="sm"
                                        >
                                            Cancel
                                        </Button>
                                    </div>
                                </div>
                            ) : (
                                <>
                                    <div className="flex justify-between items-start mb-2">
                                        <p className="text-black flex-1 whitespace-pre-wrap">{note.body}</p>
                                        {canEditNote(note) && (
                                            <div className="flex gap-1 ml-2">
                                                <Button
                                                    onClick={() => handleEditNote(note)}
                                                    variant="ghost"
                                                    size="sm"
                                                    className="text-gray-600 hover:text-black h-6 px-2"
                                                >
                                                    Edit
                                                </Button>
                                                <Button
                                                    onClick={() => handleDeleteNote(note.id)}
                                                    variant="ghost"
                                                    size="sm"
                                                    className="text-gray-600 hover:text-black h-6 px-2"
                                                >
                                                    Delete
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                    <p className="text-xs text-gray-500">
                                        By {note.user.name} on {new Date(note.created_at).toLocaleDateString()} at {new Date(note.created_at).toLocaleTimeString()}
                                    </p>
                                </>
                            )}
                        </div>
                    ))}
                </div>
            ) : (
                <div className="text-center py-8">
                    <p className="text-gray-500 mb-4">No notes yet.</p>
                    {canCreateNotes && !isAddingNote && (
                        <Button 
                            onClick={() => setIsAddingNote(true)}
                            className="bg-black text-white hover:bg-gray-800"
                        >
                            Add First Note
                        </Button>
                    )}
                </div>
            )}
        </div>
    );
}