<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\LetterTemplate;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class SuperAdminTrashController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'letters');

        $letters = null;
        $templates = null;
        $users = null;
        $categories = null;

        if ($tab === 'letters') {
            $letters = Letter::onlyTrashed()->with(['category', 'creator', 'updater'])->latest('deleted_at')->paginate(10)->withQueryString();
        } elseif ($tab === 'templates') {
            $templates = LetterTemplate::onlyTrashed()->with('uploader')->latest('deleted_at')->paginate(10)->withQueryString();
        } elseif ($tab === 'users') {
            $users = User::onlyTrashed()->latest('deleted_at')->paginate(10)->withQueryString();
        } elseif ($tab === 'categories') {
            $categories = Category::onlyTrashed()->latest('deleted_at')->paginate(10)->withQueryString();
        } else {
            abort(404);
        }

        return view('admin.trash.index', compact('tab', 'letters', 'templates', 'users', 'categories'));
    }

    public function restoreLetter($id)
    {
        $letter = Letter::onlyTrashed()->findOrFail($id);
        $letter->restore();
        return redirect()->route('super_admin.trash.index', ['tab' => 'letters'])->with('success', 'Surat berhasil dipulihkan.');
    }

    public function forceDeleteLetter($id)
    {
        $letter = Letter::onlyTrashed()->with('attachments')->findOrFail($id);
        $letter->forceDelete();
        return redirect()->route('super_admin.trash.index', ['tab' => 'letters'])->with('success', 'Surat beserta seluruh file riwayatnya telah dihapus permanen.');
    }

    public function restoreTemplate($id)
    {
        $template = LetterTemplate::onlyTrashed()->findOrFail($id);
        $template->restore();
        return redirect()->route('super_admin.trash.index', ['tab' => 'templates'])->with('success', 'Template surat berhasil dipulihkan.');
    }

    public function forceDeleteTemplate($id)
    {
        $template = LetterTemplate::onlyTrashed()->findOrFail($id);
        $template->forceDelete();
        return redirect()->route('super_admin.trash.index', ['tab' => 'templates'])->with('success', 'Template surat dan filenya berhasil dihapus permanen.');
    }

    public function restoreUser($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('super_admin.trash.index', ['tab' => 'users'])->with('success', 'Pengguna berhasil dipulihkan.');
    }

    public function forceDeleteUser($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('super_admin.trash.index', ['tab' => 'users'])->with('success', 'Pengguna berhasil dihapus permanen.');
    }

    public function restoreCategory($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        return redirect()->route('super_admin.trash.index', ['tab' => 'categories'])->with('success', 'Alur pengajuan berhasil dipulihkan.');
    }

    public function forceDeleteCategory($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();
        return redirect()->route('super_admin.trash.index', ['tab' => 'categories'])->with('success', 'Alur pengajuan berhasil dihapus permanen.');
    }
}
