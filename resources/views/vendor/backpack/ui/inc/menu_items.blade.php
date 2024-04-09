{{-- This file is used for menu items by any Backpack v6 theme --}}
<x-backpack::menu-item title="Доски" icon="las la-columns" :link="backpack_url('board')" />
<x-backpack::menu-item title="Проекты" icon="las la-project-diagram" :link="backpack_url('project')" />
<!-- <li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li> -->

<x-backpack::menu-item title="Пароли" icon="las la-key" :link="backpack_url('password')" />
<x-backpack::menu-item title="Пользователи" icon="las la-user" :link="backpack_url('user')" />
<!-- <x-backpack::menu-item title="Page Canban" icon="la la-question" :link="backpack_url('page_canban')" /> -->

<!-- <x-backpack::menu-item title="Columns" icon="la la-question" :link="backpack_url('column')" />
<x-backpack::menu-item title="Cards" icon="la la-question" :link="backpack_url('card')" /> -->