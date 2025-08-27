@extends('layouts.default')

@section('content')


    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5"><img class="rounded-circle mt-5"
                        width="150px"
                        src="{{ $user?->userinfo?->image == null ? 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg' 
                        : asset('storage/' . $user->userinfo->image) }}"
                        class="font-weight-bold">{{ $user->name }}</span><span
                        class="text-black-50">{{ $user->phone }}</span><span> </span></div>
            </div>
            <form method="POST" action="{{ route('updateUser', $user->id) }}" class="col-md-5 border-right">
                @csrf
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-right">الملف الشخصي</h4>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6"><label class="labels">الاسم</label><input type="text" class="form-control"
                                name="name" placeholder="name" value="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12"><label class="labels">رقم الهاتف</label><input type="text"
                                class="form-control" placeholder="enter phone number" name="phone"
                                value="{{ $user->phone }}">
                        </div>
                        <div class="col-md-12"><label class="labels">النوع</label><input type="text" class="form-control"
                                placeholder="gender" name="gender" value="{{ $user?->userinfo?->gender }}"></div>
                        <div class="col-md-12"><label class="labels">السن</label><input type="text" class="form-control"
                                placeholder="age" name="age" value="{{ $user?->userinfo?->age }}">
                        </div>
                        <div class="col-md-12"><label class="labels">الراتب</label><input type="text"
                                class="form-control" placeholder="sallary" name="sallary" value="{{ $user->sallary }}">
                        </div>
                        <div class="col-md-12"><label class="labels">تاريخ التعيين</label><input type="text"
                                class="form-control" placeholder="date_runer" name="date_runer"
                                value="{{ $user?->userinfo?->date_runer }}">
                        </div>
                        <div class="col-md-12"><label class="labels">رقم الاقامة</label><input type="text"
                                class="form-control" placeholder="number_residence" name="number_residence"
                                value="{{ $user?->userinfo?->number_residence }}">
                        </div>
                        <div class="col-md-12"><label class="labels">الجنسية</label><input type="text"
                                class="form-control" placeholder="nationality" name="nationality"
                                value="{{ $user?->userinfo?->nationality }}"></div>
                        <div class="col-md-12"><label class="labels">الحالة الاجتماعية</label><input type="text"
                                class="form-control" placeholder="marital_status" name="marital_status"
                                value="{{ $user?->userinfo?->marital_status }}">
                        </div>
                        <div class="col-md-12"><label class="labels">تاريخ انتهاء الاقامة</label><input type="text"
                                class="form-control" placeholder="expire_residence" name="expire_residence"
                                value="{{ $user?->userinfo?->expire_residence }}">
                        </div>
                        <div class="col-md-12"><label class="labels">كلمة السر الجديدة</label><input type="text"
                                class="form-control" name="password" placeholder="كلمة السر"></div>
                        <div class="col-md-12"><label class="labels">ارفع صورة جديدة</label><input type="file"
                                class="form-control" name="image" placeholder="صورة"></div>
                    </div>
                    <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit">Save
                            Profile</button></div>
                </div>

            </form>
            {{-- <div class="col-md-4">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center experience"><span>Edit
                        Experience</span><span class="border px-3 p-1 add-experience"><i
                            class="fa fa-plus"></i>&nbsp;Experience</span></div><br>
                <div class="col-md-12"><label class="labels">Experience in Designing</label><input type="text"
                        class="form-control" placeholder="experience" value=""></div> <br>
                <div class="col-md-12"><label class="labels">Additional Details</label><input type="text"
                        class="form-control" placeholder="additional details" value=""></div>
            </div>
        </div> --}}
        </div>
    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
@stop
