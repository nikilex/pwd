{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Пароли" icon="las la-key" :link="backpack_url('password')" />
<x-backpack::menu-item title="Проекты" icon="las la-project-diagram" :link="backpack_url('project')" />
<x-backpack::menu-item title="Пользователи" icon="las la-user" :link="backpack_url('user')" />