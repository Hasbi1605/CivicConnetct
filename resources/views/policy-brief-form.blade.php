@extends('layouts.fullwidth', ['backUrl' => route('policy-lab.index'), 'backLabel' => 'Kembali ke Policy Lab'])

@section('title', isset($brief) ? 'Edit Policy Brief' : 'Buat Policy Brief')

@section('content')
<div class="pb-form-page">
    <div class="pb-form-header">
        <a href="{{ route('policy-lab.index') }}" class="lab-back-link">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Policy Lab
        </a>
        <h1>{{ isset($brief) ? 'Edit Policy Brief' : 'Buat Policy Brief Baru' }}</h1>
        <p>Susun naskah kebijakan berbasis data. Setelah submit, agent akan mereview sebelum dipublikasikan.</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0;padding-left:16px">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="pb-form-container card">
        <form action="{{ isset($brief) ? route('policy-lab.update', $brief) : route('policy-lab.store') }}" method="POST">
            @csrf
            @if(isset($brief))
                @method('PUT')
            @endif

            <div class="pb-form-section">
                <div class="form-group">
                    <label class="form-label">Template <span style="color:#cc1016">*</span></label>
                    <div class="pb-template-select">
                        <label class="pb-template-option">
                            <input type="radio" name="template_type" value="standar" {{ old('template_type', $brief->template_type ?? $templateType ?? 'standar') === 'standar' ? 'checked' : '' }}>
                            <div class="pb-template-option-inner">
                                <span class="material-symbols-outlined">description</span>
                                <span>Standar</span>
                            </div>
                        </label>
                        <label class="pb-template-option">
                            <input type="radio" name="template_type" value="data-driven" {{ old('template_type', $brief->template_type ?? $templateType ?? '') === 'data-driven' ? 'checked' : '' }}>
                            <div class="pb-template-option-inner">
                                <span class="material-symbols-outlined">bar_chart</span>
                                <span>Data-Driven</span>
                            </div>
                        </label>
                        <label class="pb-template-option">
                            <input type="radio" name="template_type" value="quick-response" {{ old('template_type', $brief->template_type ?? $templateType ?? '') === 'quick-response' ? 'checked' : '' }}>
                            <div class="pb-template-option-inner">
                                <span class="material-symbols-outlined">bolt</span>
                                <span>Quick Response</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pb-form-section">
                <div class="form-group">
                    <label class="form-label">Judul Policy Brief <span style="color:#cc1016">*</span></label>
                    <input type="text" name="title" class="form-input" value="{{ old('title', $brief->title ?? '') }}" placeholder="Judul naskah kebijakan..." required maxlength="200">
                </div>

                <div class="form-group">
                    <label class="form-label">Ringkasan Eksekutif <span style="color:#cc1016">*</span></label>
                    <textarea name="summary" class="form-input form-textarea" rows="3" required placeholder="Ringkasan singkat isi policy brief (1-2 paragraf)..." maxlength="2000">{{ old('summary', $brief->summary ?? '') }}</textarea>
                </div>
            </div>

            <div class="pb-form-section">
                <h3 class="pb-form-section-title">Konten Utama</h3>

                <div class="form-group">
                    <label class="form-label">Ringkasan Masalah <span style="color:#cc1016">*</span></label>
                    <textarea name="problem" class="form-input form-textarea" rows="5" required placeholder="Deskripsikan permasalahan yang diangkat. Sertakan konteks, data, dan latar belakang..." maxlength="5000">{{ old('problem', $brief->problem ?? '') }}</textarea>
                    <p class="form-hint">Jelaskan masalah secara objektif dengan data dan fakta pendukung.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Analisis & Data <span style="color:#cc1016">*</span></label>
                    <textarea name="analysis" class="form-input form-textarea" rows="6" required placeholder="Analisis mendalam berbasis data. Gunakan sumber terpercaya..." maxlength="5000">{{ old('analysis', $brief->analysis ?? '') }}</textarea>
                    <p class="form-hint">Sajikan analisis kritis dari berbagai sudut pandang.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Rekomendasi Kebijakan <span style="color:#cc1016">*</span></label>
                    <textarea name="recommendation" class="form-input form-textarea" rows="5" required placeholder="Rekomendasi kebijakan yang konkret dan implementatif..." maxlength="5000">{{ old('recommendation', $brief->recommendation ?? '') }}</textarea>
                    <p class="form-hint">Berikan rekomendasi yang spesifik, terukur, dan dapat dilaksanakan.</p>
                </div>
            </div>

            <div class="pb-form-actions">
                <button type="submit" name="action" value="draft" class="btn-outline">
                    <span class="material-symbols-outlined" style="font-size:18px">save</span>
                    Simpan Draft
                </button>
                <button type="submit" name="action" value="submit" class="btn-primary" onclick="return confirm('Submit ke review Agent? Pastikan isi sudah lengkap.')">
                    <span class="material-symbols-outlined" style="font-size:18px">send</span>
                    Submit ke Review Agent
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
