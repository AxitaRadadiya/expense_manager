@extends('admin.layouts.app')
@section('title', 'Create User')

@section('content')

<style>
  /* ── Page Header ── */
  .page-hero {
    background: linear-gradient(135deg, #0B1120 0%, #111C30 60%, #162244 100%);
    padding: 1.6rem 2rem 4.2rem;
    position: relative; overflow: hidden;
  }
  .page-hero::before {
    content:''; position:absolute; inset:0; pointer-events:none;
    background-image: radial-gradient(rgba(201,150,12,.15) 1px, transparent 1px);
    background-size: 26px 26px;
  }
  .page-hero .orb {
    position:absolute; border-radius:50%; pointer-events:none;
    width:180px; height:180px;
    background:radial-gradient(circle,rgba(201,150,12,.2) 0%,transparent 65%);
    top:-50px; right:40px;
  }
  .page-hero h1 {
    font-family:'Playfair Display',serif;
    font-size:1.45rem; font-weight:800; color:#fff; margin:0 0 .25rem;
    position:relative; z-index:2;
  }
  .page-hero p { color:rgba(255,255,255,.4); font-size:.82rem; margin:0; position:relative; z-index:2; }

  /* ── Pull-up form card ── */
  .pull-card { margin-top:-2.4rem; position:relative; z-index:10; padding:0 1.5rem; }
  .form-card {
    background:#fff; border-radius:16px;
    box-shadow:0 6px 32px rgba(0,0,0,.1);
    border:1px solid rgba(0,0,0,.04);
    overflow:hidden;
  }
  .form-card-head {
    padding:1.1rem 1.5rem; border-bottom:1px solid #f0f2f7;
    display:flex; align-items:center; justify-content:space-between;
  }
  .form-card-title { font-size:.92rem; font-weight:800; color:#0D1A30; display:flex; align-items:center; gap:.5rem; }
  .form-card-title i { color:#C9960C; }
  .form-card-body { padding:1.75rem 1.5rem; }

  /* ── Field styling ── */
  .frow { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
  @media(max-width:640px){ .frow { grid-template-columns:1fr; } }
  .frow-3 { grid-template-columns:1fr 1fr 1fr; }
  @media(max-width:768px){ .frow-3 { grid-template-columns:1fr 1fr; } }
  @media(max-width:480px){ .frow-3 { grid-template-columns:1fr; } }
  .frow-full { grid-template-columns:1fr; }

  .fgroup { display:flex; flex-direction:column; }
  .flabel {
    font-size:.76rem; font-weight:700; color:#334155;
    margin-bottom:.42rem; letter-spacing:.02em;
    display:flex; align-items:center; gap:.3rem;
  }
  .flabel .req { color:#dc2626; }

  .finput, .fselect, .ftextarea {
    width:100%;
    border:1.5px solid #dde2ec;
    border-radius:8px;
    padding:.52rem .85rem;
    font-size:.86rem; color:#0D1A30;
    font-family:'DM Sans',sans-serif;
    background:#fff;
    transition:border-color .2s, box-shadow .2s;
    outline:none;
  }
  .finput:focus, .fselect:focus, .ftextarea:focus {
    border-color:#C9960C;
    box-shadow:0 0 0 3px rgba(201,150,12,.12);
  }
  .finput.is-invalid, .fselect.is-invalid, .ftextarea.is-invalid {
    border-color:#dc2626;
    box-shadow:0 0 0 3px rgba(220,38,38,.08);
  }
  .fselect { cursor:pointer; }
  .ftextarea { resize:vertical; min-height:90px; }

  /* Password wrapper */
  .pw-wrap { position:relative; }
  .pw-wrap .finput { padding-right:2.5rem; }
  .pw-eye {
    position:absolute; right:.75rem; top:50%; transform:translateY(-50%);
    background:none; border:none; cursor:pointer; padding:0;
    display:flex; align-items:center; color:#94a3b8;
  }
  .pw-eye:hover { color:#C9960C; }

  /* Error text */
  .ferr { font-size:.74rem; color:#dc2626; margin-top:.3rem; }

  /* Section divider */
  .fsection {
    font-size:.68rem; font-weight:800; text-transform:uppercase;
    letter-spacing:1.6px; color:#8a98b4;
    display:flex; align-items:center; gap:.6rem;
    margin:1.75rem 0 1.1rem;
  }
  .fsection::after { content:''; flex:1; height:1px; background:#e8eaf0; }
  .fsection .fsi {
    width:20px; height:20px;
    background:linear-gradient(135deg,#9A6E00,#C9960C);
    border-radius:5px; display:inline-flex; align-items:center; justify-content:center;
    font-size:.58rem; color:#fff;
  }

  /* Buttons */
  .btn-submit {
    background:linear-gradient(135deg,#9A6E00,#F5BE2E);
    color:#111; border:none; border-radius:8px;
    padding:.55rem 1.4rem; font-size:.86rem; font-weight:700;
    font-family:'DM Sans',sans-serif; cursor:pointer;
    display:inline-flex; align-items:center; gap:.4rem;
    box-shadow:0 3px 12px rgba(201,150,12,.3);
    transition:transform .18s,box-shadow .18s;
  }
  .btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(201,150,12,.4); }
  .btn-cancel {
    background:#f1f5f9; color:#64748b; border:1.5px solid #e2e8f0;
    border-radius:8px; padding:.52rem 1.2rem;
    font-size:.86rem; font-weight:600; text-decoration:none;
    display:inline-flex; align-items:center; gap:.4rem;
    transition:background .18s;
  }
  .btn-cancel:hover { background:#e8edf5; color:#334155; }
</style>

{{-- HERO --}}
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-user-plus mr-2" style="color:#C9960C;font-size:1rem;"></i>Create User</h1>
    <p>Fill in the details below to add a new system user.</p>
  </div>
</div>

{{-- FORM CARD --}}
<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="form-card">
      <div class="form-card-head">
        <div class="form-card-title">
          <i class="fas fa-user-edit"></i> User Details
        </div>
        <a href="{{ route('users.index') }}" class="btn-cancel">
          <i class="fas fa-arrow-left"></i> Back
        </a>
      </div>

      <div class="form-card-body">
        <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
          @csrf

          {{-- ── Basic Info ── --}}
          <div class="fsection"><span class="fsi"><i class="fas fa-id-card"></i></span> Basic Info</div>

          <div class="frow">
            {{-- Name --}}
            <div class="fgroup">
              <label class="flabel" for="name">Full Name <span class="req">*</span></label>
              <input id="name" name="name" type="text" class="finput @error('name') is-invalid @enderror"
                     value="{{ old('name') }}" placeholder="e.g. John Doe" required>
              @error('name') <span class="ferr">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div class="fgroup">
              <label class="flabel" for="email">Email Address <span class="req">*</span></label>
              <input id="email" name="email" type="email" class="finput @error('email') is-invalid @enderror"
                     value="{{ old('email') }}" placeholder="user@example.com" required>
              @error('email') <span class="ferr">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="frow frow-3">
            {{-- Mobile --}}
            <div class="fgroup">
              <label class="flabel" for="mobile">Mobile Number</label>
              <input id="mobile" name="mobile" type="text" class="finput @error('mobile') is-invalid @enderror"
                     value="{{ old('mobile') }}" placeholder="+91 98765 43210" maxlength="15">
              @error('mobile') <span class="ferr">{{ $message }}</span> @enderror
            </div>

            {{-- Role --}}
            <div class="fgroup">
              <label class="flabel" for="role_id">Role <span class="req">*</span></label>
              <select id="role_id" name="role_id" class="fselect @error('role_id') is-invalid @enderror" required>
                <option value="">— Select Role —</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                  </option>
                @endforeach
              </select>
              @error('role_id') <span class="ferr">{{ $message }}</span> @enderror
            </div>

            {{-- Status --}}
            <div class="fgroup">
              <label class="flabel" for="status">Status <span class="req">*</span></label>
              <select id="status" name="status" class="fselect @error('status') is-invalid @enderror" required>
                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
              </select>
              @error('status') <span class="ferr">{{ $message }}</span> @enderror
            </div>
          </div>

          {{-- ── Password ── --}}
          <div class="fsection"><span class="fsi"><i class="fas fa-lock"></i></span> Password</div>

          <div class="frow">
            {{-- Password --}}
            <div class="fgroup">
              <label class="flabel" for="password">Password <span class="req">*</span></label>
              <div class="pw-wrap">
                <input id="password" name="password" type="password"
                       class="finput @error('password') is-invalid @enderror"
                       placeholder="Min. 8 characters" required>
                <button type="button" class="pw-eye" onclick="togglePw('password','eye1')">
                  <i id="eye1" class="fas fa-eye" style="font-size:.85rem;"></i>
                </button>
              </div>
              @error('password') <span class="ferr">{{ $message }}</span> @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="fgroup">
              <label class="flabel" for="password_confirmation">Confirm Password <span class="req">*</span></label>
              <div class="pw-wrap">
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="finput" placeholder="Re-enter password" required>
                <button type="button" class="pw-eye" onclick="togglePw('password_confirmation','eye2')">
                  <i id="eye2" class="fas fa-eye" style="font-size:.85rem;"></i>
                </button>
              </div>
            </div>
          </div>

          {{-- ── Note ── --}}
          <div class="fsection"><span class="fsi"><i class="fas fa-sticky-note"></i></span> Additional Info</div>

          <div class="frow frow-full">
            <div class="fgroup">
              <label class="flabel" for="note">Note</label>
              <textarea id="note" name="note" class="ftextarea @error('note') is-invalid @enderror"
                        placeholder="Optional note about this user…">{{ old('note') }}</textarea>
              @error('note') <span class="ferr">{{ $message }}</span> @enderror
            </div>
          </div>

          {{-- ── Actions ── --}}
          <div class="d-flex align-items-center" style="gap:.75rem;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #f0f2f7;">
            <button type="submit" class="btn-submit">
              <i class="fas fa-user-check"></i> Create User
            </button>
            <a href="{{ route('users.index') }}" class="btn-cancel">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<div style="height:2rem;"></div>

<script>
function togglePw(inputId, iconId) {
  var inp = document.getElementById(inputId);
  var ico = document.getElementById(iconId);
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    inp.type = 'password';
    ico.classList.replace('fa-eye-slash', 'fa-eye');
  }
}
</script>

@endsection