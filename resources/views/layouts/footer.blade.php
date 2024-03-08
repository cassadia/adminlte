<!-- To the right -->
<div class="float-right d-none d-sm-inline">
    {{-- Anything you want --}}
    {{ $content->footer_text_right }}
</div>
<!-- Default to the left -->
<strong>
    {{-- Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>. --}}
    {{ $content->footer_text_left }}
</strong>
    @if($content->footer_text_middle == null)
        &nbsp;
    @else
        $content->footer_text_middle;
    @endif