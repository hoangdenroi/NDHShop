{{-- Music Player Panel - Floating music panel giống giỏ hàng --}}
<div x-data="musicPlayer()" x-init="init()" @keydown.escape.window="panelOpen = false"
    @toggle-music-panel.window="panelOpen = !panelOpen" @close-music-panel.window="panelOpen = false">

    {{-- Panel nhạc - nằm bên trái FAB buttons --}}
    <div x-show="panelOpen" x-cloak @click.outside="panelOpen = false"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="fixed bottom-[228px] md:bottom-[176px] right-[96px] md:right-[108px] w-[calc(100vw-112px)] sm:w-[380px] max-w-[400px] bg-white dark:bg-slate-800 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-200 dark:border-slate-700 z-[200] origin-bottom-right overflow-visible">

        {{-- Mũi tên trỏ sang phải (vào nút nhạc) --}}
        <div
            class="absolute bottom-[19px] md:bottom-[23px] -right-[9px] w-0 h-0 border-t-[9px] border-t-transparent border-b-[9px] border-b-transparent border-l-[9px] border-l-slate-200 dark:border-l-slate-700">
        </div>
        <div
            class="absolute bottom-[21px] md:bottom-[25px] -right-[7px] w-0 h-0 border-t-[7px] border-t-transparent border-b-[7px] border-b-transparent border-l-[7px] border-l-white dark:border-l-slate-800">
        </div>

        {{-- Wrapper overflow để nội dung không tràn bo tròn --}}
        <div class="rounded-xl overflow-hidden">

            {{-- Header --}}
            <div
                class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-gradient-to-r from-purple-500/10 to-pink-500/10 dark:from-purple-500/20 dark:to-pink-500/20">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-500">library_music</span>
                    <h3 class="font-bold text-slate-900 dark:text-white text-[15px]">Nghe nhạc</h3>
                </div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400"
                    x-text="filteredTracks.length + ' bài'"></span>
            </div>

            {{-- Ô tìm kiếm --}}
            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700/50">
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Tìm bài hát..."
                        class="w-full pl-9 pr-8 py-2 text-sm rounded-lg bg-slate-100 dark:bg-slate-700/50 border-0 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-purple-500/50 outline-none transition-all">
                    <button x-show="searchQuery" @click="searchQuery = ''" x-cloak
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
            </div>

            {{-- Danh sách nhạc --}}
            <div
                class="max-h-[285px] overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-300 dark:[&::-webkit-scrollbar-thumb]:bg-slate-600 [&::-webkit-scrollbar-thumb]:rounded-full">

                {{-- Loading --}}
                <div x-show="loading" class="flex items-center justify-center py-10">
                    <div class="w-6 h-6 border-2 border-purple-500 border-t-transparent rounded-full animate-spin">
                    </div>
                </div>

                {{-- Danh sách bài hát --}}
                <template x-for="(track, index) in filteredTracks" :key="track.id">
                    <button @click="playTrack(index)"
                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group text-left"
                        :class="{ 'bg-purple-50 dark:bg-purple-900/20': currentTrack && currentTrack.id === track.id }">

                        {{-- Số thứ tự / Icon đang phát --}}
                        <div class="w-7 h-7 flex items-center justify-center shrink-0 rounded-full"
                            :class="currentTrack && currentTrack.id === track.id ? 'bg-purple-500 text-white' : 'text-slate-400'">
                            <span x-show="!(currentTrack && currentTrack.id === track.id)" class="text-xs font-bold"
                                x-text="index + 1"></span>
                            <span x-show="currentTrack && currentTrack.id === track.id"
                                class="material-symbols-outlined text-sm"
                                x-text="isPlaying ? 'equalizer' : 'play_arrow'"></span>
                        </div>

                        {{-- Tên bài + Mô tả --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold truncate"
                                :class="currentTrack && currentTrack.id === track.id ? 'text-purple-600 dark:text-purple-400' : 'text-slate-900 dark:text-white'"
                                x-text="track.name"></p>
                            <p class="text-[11px] text-slate-400 truncate"
                                x-text="track.description || 'Không có mô tả'"></p>
                        </div>

                        {{-- Kích thước file --}}
                        <span class="text-[10px] text-slate-400 shrink-0" x-show="track.file_size"
                            x-text="track.file_size"></span>
                    </button>
                </template>

                {{-- Không tìm thấy --}}
                <div x-show="!loading && filteredTracks.length === 0" x-cloak
                    class="flex flex-col items-center justify-center py-10 px-4 text-slate-500 dark:text-slate-400">
                    <span class="material-symbols-outlined text-[40px] mb-2 opacity-30">music_off</span>
                    <p class="text-sm font-medium"
                        x-text="searchQuery ? 'Không tìm thấy bài hát' : 'Chưa có bài hát nào'"></p>
                </div>
            </div>

            {{-- Player controls (khi đang phát) --}}
            <div x-show="currentTrack" x-cloak
                class="px-4 py-3 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-700/30">

                {{-- Tên bài đang phát --}}
                <p class="text-xs font-bold text-purple-600 dark:text-purple-400 truncate mb-2"
                    x-text="currentTrack?.name"></p>

                {{-- Thanh tiến trình --}}
                <div class="relative w-full h-1 bg-slate-200 dark:bg-slate-600 rounded-full mb-2 cursor-pointer group"
                    @click="seekTo($event)">
                    <div class="absolute top-0 left-0 h-full bg-purple-500 rounded-full transition-all duration-100"
                        :style="'width:' + progress + '%'"></div>
                    <div class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-purple-500 rounded-full shadow opacity-0 group-hover:opacity-100 transition-opacity"
                        :style="'left: calc(' + progress + '% - 6px)'"></div>
                </div>

                {{-- Thời gian + Controls --}}
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-slate-400"
                        x-text="formatTime(currentTime) + ' / ' + formatTime(duration)"></span>

                    <div class="flex items-center gap-1">
                        {{-- Bài trước --}}
                        <button @click="prevTrack()" class="p-1 text-slate-500 hover:text-purple-500 transition-colors">
                            <span class="material-symbols-outlined text-lg">skip_previous</span>
                        </button>

                        {{-- Play/Pause --}}
                        <button @click="togglePlay()"
                            class="p-1.5 bg-purple-500 hover:bg-purple-600 text-white rounded-full transition-colors">
                            <span class="material-symbols-outlined text-lg"
                                x-text="isPlaying ? 'pause' : 'play_arrow'"></span>
                        </button>

                        {{-- Bài kế --}}
                        <button @click="nextTrack()" class="p-1 text-slate-500 hover:text-purple-500 transition-colors">
                            <span class="material-symbols-outlined text-lg">skip_next</span>
                        </button>

                        {{-- Âm lượng --}}
                        <div class="relative ml-1" x-data="{ showVol: false }">
                            <button @click="showVol = !showVol"
                                class="p-1 text-slate-500 hover:text-purple-500 transition-colors">
                                <span class="material-symbols-outlined text-lg"
                                    x-text="volume === 0 ? 'volume_off' : (volume < 0.5 ? 'volume_down' : 'volume_up')"></span>
                            </button>
                            <div x-show="showVol" x-cloak @click.outside="showVol = false"
                                class="absolute bottom-full right-0 mb-2 p-2 bg-white dark:bg-slate-700 rounded-lg shadow-lg border border-slate-200 dark:border-slate-600">
                                <input type="range" min="0" max="1" step="0.05" x-model="volume"
                                    @input="audio.volume = volume" class="w-20 h-1 accent-purple-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End of wrapper -->
    </div> <!-- End of panelOpen -->
</div> <!-- End of root -->


<script>
    /**
     * Alpine.js component: Music Player (Singleton Pattern)
     * Hỗ trợ phát nhạc MP3 (native) và link Youtube (Iframe API ẩn)
     * 
     * SINGLETON: State được lưu vào window.__musicPlayerState
     * để tái sử dụng sau mỗi Turbo navigate (data-turbo-permanent).
     * Init() chỉ chạy 1 lần duy nhất trong toàn bộ lifecycle.
     */
    function musicPlayer() {
        // Nếu đã có instance cũ (sau Turbo navigate), tái sử dụng state cũ
        if (window.__musicPlayerState) {
            return window.__musicPlayerState;
        }

        const state = {
            panelOpen: false,
            loading: false,
            searchQuery: '',
            tracks: [],
            filteredTracks: [],
            currentTrack: null,
            currentIndex: -1,
            isPlaying: false,

            // Native Audio
            audio: null,
            // Youtube Player
            ytPlayer: null,
            ytReady: false,
            playerType: 'native', // 'native' hoặc 'youtube'
            ytInterval: null,

            progress: 0,
            currentTime: 0,
            duration: 0,
            volume: 0.7,

            // Flag đánh dấu đã init heavy resources chưa (Audio, YT, setInterval)
            _initialized: false,

            init() {
                // Heavy init: chỉ tạo Audio, YouTube, setInterval 1 lần duy nhất
                if (!this._initialized) {
                    this._initialized = true;
                    this._heavyInit();
                }

                // Watchers: LUÔN đăng ký lại mỗi lần Alpine re-init
                // (vì destroyTree() xóa sạch $watch bindings cũ)
                this._registerWatchers();
            },

            // Khởi tạo các resource nặng - chỉ gọi 1 lần trong lifecycle
            _heavyInit() {
                // Khởi tạo Audio element native
                this.audio = new Audio();
                this.audio.volume = this.volume;

                // Sync thời gian native audio
                this.audio.addEventListener('timeupdate', () => {
                    if (this.playerType !== 'native') return;
                    this.currentTime = this.audio.currentTime;
                    this.duration = this.audio.duration || 0;
                    this.progress = this.duration > 0 ? (this.currentTime / this.duration) * 100 : 0;
                });

                this.audio.addEventListener('ended', () => {
                    if (this.playerType === 'native') this.nextTrack();
                });

                // Load Youtube Iframe API
                if (!window.YT) {
                    const tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    const firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                    window.onYouTubeIframeAPIReady = () => {
                        this._initYTPlayer();
                    };
                } else if (window.YT && window.YT.Player) {
                    this._initYTPlayer();
                }

                // Cập nhật progress cho Youtube (vì nó không có event timeupdate)
                this.ytInterval = setInterval(() => {
                    if (this.playerType === 'youtube' && this.isPlaying && this.ytReady && this.ytPlayer.getCurrentTime) {
                        this.currentTime = this.ytPlayer.getCurrentTime();
                        this.duration = this.ytPlayer.getDuration() || 0;
                        this.progress = this.duration > 0 ? (this.currentTime / this.duration) * 100 : 0;
                    }
                }, 500);
            },

            // Đăng ký Alpine $watch - gọi lại mỗi lần Alpine re-init (sau Turbo navigate)
            _registerWatchers() {
                this.$watch('panelOpen', (val) => {
                    if (val && this.tracks.length === 0) {
                        this.fetchTracks();
                    }
                });

                this.$watch('searchQuery', () => {
                    this.filterTracks();
                });

                this.$watch('volume', (val) => {
                    this.audio.volume = val;
                    if (this.ytReady) this.ytPlayer.setVolume(val * 100);
                });
            },

            // Khởi tạo YouTube Player riêng biệt, tách ra để tái sử dụng
            _initYTPlayer() {
                this.ytPlayer = new YT.Player('youtube-hidden-player', {
                    height: '0',
                    width: '0',
                    videoId: '',
                    playerVars: {
                        'playsinline': 1,
                        'controls': 0,
                        'autoplay': 0,
                        'origin': window.location.origin
                    },
                    events: {
                        'onReady': (event) => {
                            this.ytReady = true;
                            event.target.setVolume(this.volume * 100);
                        },
                        'onStateChange': (event) => {
                            if (event.data === YT.PlayerState.PLAYING) {
                                this.isPlaying = true;
                                this.duration = this.ytPlayer.getDuration();
                            } else if (event.data === YT.PlayerState.PAUSED) {
                                this.isPlaying = false;
                            } else if (event.data === YT.PlayerState.ENDED) {
                                this.nextTrack();
                            }
                        },
                        'onError': (event) => {
                            console.warn('Youtube Player Error: Mã lỗi ' + event.data + '. Tự động chuyển bài...');
                            this.nextTrack();
                        }
                    }
                });
            },

            // Hàm kiểm tra và lấy Youtube ID
            getYouTubeId(url) {
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            },

            async fetchTracks() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route("api.v1.music.tracks") }}');
                    const data = await res.json();
                    if (data.success) {
                        this.tracks = data.tracks;
                        this.filterTracks();
                    }
                } catch (e) {
                    console.error('Lỗi tải nhạc:', e);
                } finally {
                    this.loading = false;
                }
            },

            filterTracks() {
                if (!this.searchQuery.trim()) {
                    this.filteredTracks = this.tracks;
                    return;
                }
                const q = this.searchQuery.toLowerCase().trim();
                this.filteredTracks = this.tracks.filter(t =>
                    t.name.toLowerCase().includes(q) ||
                    (t.description && t.description.toLowerCase().includes(q))
                );
            },

            playTrack(index) {
                const track = this.filteredTracks[index];
                if (!track) return;

                // Nếu click bài đang phát → pause / play
                if (this.currentTrack && this.currentTrack.id === track.id) {
                    this.togglePlay();
                    return;
                }

                this.currentTrack = track;
                this.currentIndex = index;
                this.progress = 0;
                this.currentTime = 0;

                const ytId = this.getYouTubeId(track.url);

                if (ytId) {
                    // Phát Youtube
                    this.playerType = 'youtube';
                    this.audio.pause(); // Dừng native

                    if (this.ytReady) {
                        this.ytPlayer.loadVideoById(ytId);
                        this.isPlaying = true;
                    } else {
                        // Nếu YT API chưa sẵn sàng, thử lại sau 0.5s
                        setTimeout(() => {
                            if (this.ytReady) {
                                this.ytPlayer.loadVideoById(ytId);
                                this.isPlaying = true;
                            }
                        }, 500);
                    }
                } else {
                    // Phát Native MP3
                    this.playerType = 'native';
                    if (this.ytReady && this.ytPlayer.stopVideo) {
                        this.ytPlayer.stopVideo(); // Dừng YT
                    }
                    this.audio.src = track.url;
                    this.audio.play();
                    this.isPlaying = true;
                }
            },

            togglePlay() {
                if (!this.currentTrack) return;

                if (this.playerType === 'youtube' && this.ytReady) {
                    if (this.isPlaying) {
                        this.ytPlayer.pauseVideo();
                    } else {
                        this.ytPlayer.playVideo();
                    }
                } else if (this.playerType === 'native') {
                    if (this.isPlaying) {
                        this.audio.pause();
                    } else {
                        this.audio.play();
                    }
                    this.isPlaying = !this.isPlaying;
                }
            },

            nextTrack() {
                if (this.filteredTracks.length === 0) return;
                const nextIndex = (this.currentIndex + 1) % this.filteredTracks.length;
                this.playTrack(nextIndex);
            },

            prevTrack() {
                if (this.filteredTracks.length === 0) return;
                const prevIndex = this.currentIndex <= 0 ? this.filteredTracks.length - 1 : this.currentIndex - 1;
                this.playTrack(prevIndex);
            },

            seekTo(event) {
                if (!this.currentTrack || !this.duration) return;
                const rect = event.currentTarget.getBoundingClientRect();
                const percent = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
                const seekTime = percent * this.duration;

                if (this.playerType === 'youtube' && this.ytReady) {
                    this.ytPlayer.seekTo(seekTime, true);
                    this.currentTime = seekTime;
                } else if (this.playerType === 'native') {
                    this.audio.currentTime = seekTime;
                }
            },

            formatTime(seconds) {
                if (!seconds || isNaN(seconds)) return '0:00';
                const m = Math.floor(seconds / 60);
                const s = Math.floor(seconds % 60);
                return m + ':' + s.toString().padStart(2, '0');
            }
        };

        // Lưu state vào window để tái sử dụng sau Turbo navigate
        window.__musicPlayerState = state;
        return state;
    }
</script>