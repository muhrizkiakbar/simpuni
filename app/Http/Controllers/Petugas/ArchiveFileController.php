<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArchiveFiles\CreateRequest;
use App\Http\Requests\Admin\ArchiveFiles\UpdateRequest;
use App\Models\ArchiveFile;
use App\Outputs\Admin\ArchiveFileOutput;
use App\Services\ArchiveFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveFileController extends Controller
{
    protected $archiveFileService;

    public function __construct()
    {
        $this->archiveFileService = new ArchiveFileService(Auth::user());
    }

    //
    public function index(Request $request)
    {
        $archive_files = $this->archiveFileService->archive_files($request)->cursorPaginate(10);

        return $this->render_json_array(ArchiveFileOutput::class, "format", $archive_files);
    }

    public function show(string $id)
    {
        $archive_file = $this->archiveFileService->show(decrypt($id));
        return $this->render_json(ArchiveFileOutput::class, "format", $archive_file);
    }
}
