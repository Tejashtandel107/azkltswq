<!-- Page Breadcrumbs -->
@if(isset($pagetitle) or isset($breadcrumbs))
    <div class="page-heading">
    <h1 class="page-title">{{(isset($pagetitle)) ? $pagetitle : "" }}</h1>
    @if(isset($breadcrumbs))
        <ol class="breadcrumb">
        @foreach ($breadcrumbs as $title=>$url)
            <li class="breadcrumb-item">
                @if($loop->first)
                    @if(strlen($url)>0)
                        <a href="{{$url}}"><i class="la la-home font-20"></i></a>
                    @else
                        <i class="la la-home font-20"></i></a>
                    @endif
                @else
                    @if(strlen($url)>0)
                        <a href="{{$url}}">{{$title}}</a>
                    @else
                        {{$title}}
                    @endif
                @endif
            </li>
        @endforeach
        </ol>
    @endif
    </div>
@endif
