@extends('voyager::master')

@section('content')
    <div id="dashboard" class="page-content">
        @include('voyager::alerts')
        @include('voyager::dimmers')
        <div class="parser-section">
            <h3>For start parsing process run <span class="parser-section__highlighted">php artisan parser:parse_alcopa</span> command in terminal</h3>
        </div>

{{--        <div class="parser-section">--}}
{{--            <a @click.prevent="onParserStart" class="parser-section__button" type="button">--}}
{{--                Start parser--}}
{{--            </a>--}}
{{--        </div>--}}
{{--        <div ref="terminal" v-if="parserStarted" class="parser-section__terminal">--}}
{{--            <span v-for="(entry, index) in log">@{{ entry }}</span>--}}
{{--        </div>--}}
    </div>
@stop

@section('javascript')

    <script>
        const app = new Vue({
            el: '#dashboard',
            data: {
                parserStarted: false,
                log: []
            },
            methods: {
                onParserStart() {
                    let url = '  {{ route('start_parser') }} ';
                    axios.post(url)
                        .then(response => {
                            this.parserStarted = true;
                            setInterval(this.getProgress, 3000)
                        })
                        .catch(error => {
                            alert('Something went wrong');
                        })
                },
                getProgress() {
                    let url = '  {{ route('parser_progress') }} ';
                    axios.post(url, {log: JSON.stringify(this.log)})
                        .then(response => {
                            let new_entries = response.data.data;
                            console.log('new', new_entries)
                            this.log = [...this.log, ...new_entries]
                        })
                        .catch(error => {
                            console.log('error', error);
                        })
                }
            }
        });
    </script>

@stop
