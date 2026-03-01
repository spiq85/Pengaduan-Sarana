<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::latest()->paginate(10);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:students,nis|max:10',
            'username' => 'required|string|max:255',
            'password' => 'required|min:6',
            'class' => 'required'
        ]);

        Student::create([
            'nis' => $request->nis,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'class' => $request->class,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Siswa Berhasil Ditambahkan');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(REquest $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|unique:students,nis,' . $student->id_student . ',id_student',
            'username' => 'required|unique:students,username,' . $student->id_student . ',id_student',
            'password' => 'nullable|min:6',
            'class' => 'required',
        ]);

        $data = $request->only('nis', 'username', 'class');

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $student->update($data);

        return redirect()->route('admin.students.index')->with('success', 'Akun siswa berhasil diperbarui');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        
        return back()->with('success', 'Akun siswa berhasil di hapus');
    }
}
