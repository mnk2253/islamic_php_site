<?php
// Handle Like and Unlike POST before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Like (only if not already liked by this user)
    if (isset($_POST['like_video_id'])) {
        $like_id = (int)$_POST['like_video_id'];
        $user_likes = isset($_COOKIE['video_likes']) ? explode(',', $_COOKIE['video_likes']) : [];
        if (!in_array($like_id, $user_likes)) {
            $likes_data = file_exists('video_likes.json') ? json_decode(file_get_contents('video_likes.json'), true) : [];
            if (!isset($likes_data[$like_id])) $likes_data[$like_id] = 0;
            $likes_data[$like_id]++;
            file_put_contents('video_likes.json', json_encode($likes_data));
            $user_likes[] = $like_id;
            setcookie('video_likes', implode(',', $user_likes), time()+60*60*24*365, '/');
        }
        header('Location: index.php');
        exit();
    }
    // Unlike (remove like if user had liked)
    if (isset($_POST['unlike_video_id'])) {
        $unlike_id = (int)$_POST['unlike_video_id'];
        $user_likes = isset($_COOKIE['video_likes']) ? explode(',', $_COOKIE['video_likes']) : [];
        if (in_array($unlike_id, $user_likes)) {
            $likes_data = file_exists('video_likes.json') ? json_decode(file_get_contents('video_likes.json'), true) : [];
            if (!isset($likes_data[$unlike_id])) $likes_data[$unlike_id] = 0;
            if ($likes_data[$unlike_id] > 0) $likes_data[$unlike_id]--;
            file_put_contents('video_likes.json', json_encode($likes_data));
            $user_likes = array_diff($user_likes, [$unlike_id]);
            setcookie('video_likes', implode(',', $user_likes), time()+60*60*24*365, '/');
        }
        header('Location: index.php');
        exit();
    }
}
include('includes/header.php'); ?>
<style>
    body { font-family: 'Noto Sans Bengali', Arial, sans-serif; background: #f4f8fb; margin: 0; padding: 0; }
    h1 { text-align: center; color: #1a4d2e; margin-top: 30px; }
    nav { background: #1a4d2e; margin: 30px 0 0 0; }
    nav ul { list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; }
    nav ul li { margin: 0 18px; }
    nav ul li a { color: #fff; text-decoration: none; font-size: 20px; padding: 12px 18px; display: block; border-radius: 4px; transition: background 0.2s; }
    nav ul li a:hover { background: #388e3c; }
    section { max-width: 900px; margin: 40px auto; background: #fff; padding: 32px 28px; border-radius: 10px; box-shadow: 0 2px 12px #cfd8dc; text-align: center; }
    h2 { color: #388e3c; }
    p { font-size: 18px; color: #333; }
    .post-list { margin-top: 30px; text-align: left; }
    .post-block { margin-bottom: 32px; padding-bottom: 18px; border-bottom: 1px solid #e0e0e0; }
    .post-title { color: #1a4d2e; font-size: 20px; font-weight: bold; }
    .post-meta { color: #388e3c; font-size: 15px; margin-bottom: 6px; }
    .post-content { color: #333; font-size: 17px; }
</style>
<h1 class="rainbow-text">ইসলামী জ্ঞান</h1>
<style>
.rainbow-text {
  background: linear-gradient(90deg, #ff0000, #ff9900, #ffee00, #33ff00, #00ffee, #0066ff, #cc00ff, #ff0080, #ff0000);
  background-size: 200% auto;
  color: #fff;
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: rainbow-move 2.5s linear infinite;
  font-weight: bold;
}
@keyframes rainbow-move {
  0% { background-position: 0% 50%; }
  100% { background-position: 100% 50%; }
}
</style>


<nav>
    <ul>
        <li><a href="pages/namaz.php">নামাজ</a></li>
        <li><a href="pages/quran.php">কুরআন</a></li>
        <li><a href="pages/hadith.php">হাদীস</a></li>
        <li><a href="pages/videos.php">ইসলামী ভিডিও</a></li>
        <li><a href="pages/contact.php">যোগাযোগ</a></li>
    </ul>
</nav>
<div style="max-width:900px;margin:20px auto 0 auto;text-align:center;">
    <form method="get" style="display:inline-block;width:100%;max-width:500px;">
        <input type="text" name="search" placeholder="অনুসন্ধান করুন..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="width:70%;padding:10px;font-size:17px;border:1px solid #ccc;border-radius:4px;">
        <button type="submit" style="padding:10px 22px;font-size:17px;background:#007b5e;color:#fff;border:none;border-radius:4px;">অনুসন্ধান</button>
    </form>
</div>

<style>
@media (max-width: 600px) {
  #namaz-times { padding: 8px 0 4px 0 !important; }
  #namaz-times h2 { font-size: 19px !important; }
  #current-time { font-size: 15px !important; padding: 4px 0 !important; }
  #user-address { font-size: 14px !important; }
  #prayer-times table { font-size: 14px !important; }
  #prayer-times td { padding: 4px 6px !important; }
  #namaz-notice-slide, #prayer-times div { font-size: 12px !important; }
}
</style>
<div id="namaz-times" style="max-width:900px;margin:20px auto 20px auto;text-align:center;background:#fff;padding:18px 0 10px 0;border-radius:8px;box-shadow:0 2px 12px #cfd8dc;">
    <h2 style="color:#1a4d2e;letter-spacing:1px;font-size:28px;margin-bottom:8px;text-shadow:0 2px 8px #b2dfdb;">আপনার এলাকার নামাজের সময়</h2>
    <button id="location-permission-btn" style="display:none;margin-bottom:10px;padding:7px 18px;font-size:16px;background:#007b5e;color:#fff;border:none;border-radius:5px;cursor:pointer;">📍 অবস্থান অনুমতি দিন</button>
    <div id="current-time" style="color:#007b5e;font-size:20px;font-weight:bold;margin-bottom:8px;background:#e0f2f1;padding:6px 0;border-radius:6px;box-shadow:0 1px 4px #b2dfdb;display:inline-block;transition:box-shadow 0.2s,transform 0.2s;"></div>
    <div id="location-status" style="color:#c62828;"></div>
    <div id="user-address" style="color:#388e3c;font-size:18px;margin-bottom:10px;"></div>
    <div id="prayer-times"></div>
</div>
<script>
// Show current time in Bangla
function showCurrentTime() {
    const now = new Date();
    // Format: hh:mm:ss AM/PM
    let h = now.getHours();
    let m = now.getMinutes();
    let s = now.getSeconds();
    let ampm = h >= 12 ? 'PM' : 'AM';
    let h12 = h % 12;
    if (h12 === 0) h12 = 12;
    let timeStr = h12.toString().padStart(2, '0') + ':' + m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0') + ' ' + ampm;
    // Bangla digits
    const en2bn = n => n.replace(/[0-9]/g, d => '০১২৩৪৫৬৭৮৯'[d]);
    let banglaTime = en2bn(timeStr);
    var el = document.getElementById('current-time');
    el.innerText = 'বর্তমান সময়: ' + banglaTime;
    // Add shake animation
    el.style.transform = 'scale(1.08) rotate(-1.5deg)';
    el.style.boxShadow = '0 2px 16px #26a69a';
    setTimeout(function() {
        el.style.transform = 'scale(1) rotate(0)';
        el.style.boxShadow = '0 1px 4px #b2dfdb';
    }, 180);
}
setInterval(showCurrentTime, 1000);
showCurrentTime();
function to12Hour(time24) {
    // Remove any non-digit/colon chars (sometimes API returns 24:00 or 04:00 (24h format))
    let t = time24.trim().split(' ')[0];
    let [h, m] = t.split(':');
    h = parseInt(h, 10);
    let ampm = h >= 12 ? 'PM' : 'AM';
    if (h === 0) {
        h = 12;
    } else if (h > 12) {
        h = h - 12;
    }
    return h + ':' + m + ' ' + ampm;
}
function fetchPrayerTimes(lat, lon) {
    // Use Aladhan API (default, reliable, free)
    var url = `https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${lon}&method=8`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.code === 200) {
                var t = data.data.timings;
                var icons = {
                    Fajr: '🌅',
                    Sunrise: '☀️',
                    Dhuhr: '🏙️',
                    Asr: '🌇',
                    Maghrib: '🌆',
                    Isha: '🌃'
                };
                var rakatInfo = {
                  Fajr: 'ফজর: ২ রাকাত সুন্নত + ২ রাকাত ফরজ',
                  Sunrise: 'সূর্যোদয়: নামাজ নেই',
                  Dhuhr: 'যোহর: ৪ রাকাত সুন্নত + ৪ রাকাত ফরজ + ২ রাকাত সুন্নত + ২ রাকাত নফল',
                  Asr: 'আসর: ৪ রাকাত সুন্নত (গায়রে মুআক্কাদা) + ৪ রাকাত ফরজ',
                  Maghrib: 'মাগরিব: ৩ রাকাত ফরজ + ২ রাকাত সুন্নত + ২ রাকাত নফল',
                  Isha: 'এশা: ৪ রাকাত সুন্নত + ৪ রাকাত ফরজ + ২ রাকাত সুন্নত + ২ রাকাত নফল + ৩ রাকাত বিতর',
                };
                var html = `<table id='namaz-table' style='margin:0 auto;font-size:18px;background:#f9fbe7;border-radius:8px;box-shadow:0 1px 8px #cfd8dc;' cellpadding='8'>` +
                    `<tr><td class='namaz-cell' data-namaz='Fajr'>${icons.Fajr} ফজর</td><td style='padding-left:15px;'>${to12Hour(t.Fajr)}</td></tr>` +
                    `<tr><td class='namaz-cell' data-namaz='Sunrise'>${icons.Sunrise} সূর্যোদয়</td><td style='padding-left:15px;'>${to12Hour(t.Sunrise)}</td></tr>` +
                    `<tr><td class='namaz-cell' data-namaz='Dhuhr'>${icons.Dhuhr} যোহর</td><td style='padding-left:15px;'>${to12Hour(t.Dhuhr)}</td></tr>` +
                    `<tr><td class='namaz-cell' data-namaz='Asr'>${icons.Asr} আসর</td><td style='padding-left:15px;'>${to12Hour(t.Asr)}</td></tr>` +
                    `<tr><td class='namaz-cell' data-namaz='Maghrib'>${icons.Maghrib} মাগরিব</td><td style='padding-left:15px;'>${to12Hour(t.Maghrib)}</td></tr>` +
                    `<tr><td class='namaz-cell' data-namaz='Isha'>${icons.Isha} এশা</td><td style='padding-left:15px;'>${to12Hour(t.Isha)}</td></tr>` +
                    `</table>`;
                html += `<div style='color:#1565c0;font-size:15px;margin-top:10px;background:#e3f2fd;padding:8px 0;border-radius:6px;box-shadow:0 1px 4px #90caf9;'>এই সময়সূচীটি আজকের জন্য প্রযোজ্য</div>`;
                html += `<div id='namaz-notice-slide' style='color:#c62828;font-size:15px;margin-top:10px;background:#fff3e0;padding:8px 0;border-radius:6px;box-shadow:0 1px 4px #ffe0b2;overflow:hidden;white-space:nowrap;position:relative;height:28px;'><span id='namaz-notice-text' style='display:inline-block;position:absolute;left:100%;will-change:transform;'>নোটিশ: নামাজের সময় সামান্য পরিবর্তন হতে পারে, তাই নামাজের আগে স্থানীয় মসজিদের ইমামের সাথে পরামর্শ করুন।</span></div>`;
                document.getElementById('prayer-times').innerHTML = html;
                // Sliding notice effect
                setTimeout(function() {
                  var notice = document.getElementById('namaz-notice-text');
                  var slideBox = document.getElementById('namaz-notice-slide');
                  if (notice && slideBox) {
                    var boxWidth = slideBox.offsetWidth;
                    var textWidth = notice.offsetWidth;
                    var start = boxWidth;
                    var end = -textWidth;
                    var duration = 12000; // 12 seconds for full slide
                    function slide() {
                      notice.style.transition = 'none';
                      notice.style.left = start + 'px';
                      setTimeout(function() {
                        notice.style.transition = 'left ' + (duration/1000) + 's linear';
                        notice.style.left = end + 'px';
                      }, 100);
                    }
                    notice.addEventListener('transitionend', slide);
                    slide();
                  }
                }, 300);
                // Namaz rakat info tooltip on mouse move
                setTimeout(function() {
                  var cells = document.querySelectorAll('.namaz-cell');
                  var tooltip = document.createElement('div');
                  tooltip.id = 'namaz-tooltip';
                  tooltip.style.position = 'fixed';
                  tooltip.style.background = '#fffde7';
                  tooltip.style.color = '#1a4d2e';
                  tooltip.style.border = '1px solid #ffe082';
                  tooltip.style.padding = '7px 16px';
                  tooltip.style.borderRadius = '7px';
                  tooltip.style.boxShadow = '0 2px 8px #ffe082';
                  tooltip.style.fontSize = '16px';
                  tooltip.style.zIndex = '9999';
                  tooltip.style.pointerEvents = 'none';
                  tooltip.style.display = 'none';
                  document.body.appendChild(tooltip);
                  cells.forEach(function(cell) {
                    cell.addEventListener('mousemove', function(e) {
                      var namaz = cell.getAttribute('data-namaz');
                      if (rakatInfo[namaz]) {
                        tooltip.innerText = rakatInfo[namaz];
                        tooltip.style.display = 'block';
                        tooltip.style.left = (e.clientX + 18) + 'px';
                        tooltip.style.top = (e.clientY + 8) + 'px';
                      }
                    });
                    cell.addEventListener('mouseleave', function() {
                      tooltip.style.display = 'none';
                    });
                  });
                }, 400);
            } else {
                document.getElementById('prayer-times').innerHTML = 'নামাজের সময় পাওয়া যায়নি।';
            }
        })
        .catch(() => {
            document.getElementById('prayer-times').innerHTML = 'নামাজের সময় আনতে সমস্যা হয়েছে।';
        });
}
function fetchAddress(lat, lon) {
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data && data.address) {
                let area = data.address.union || data.address.suburb || data.address.municipality || data.address.locality || data.display_name;
                if (area) {
                    document.getElementById('user-address').innerText = 'ইউনিয়ন/এলাকা: ' + area;
                } else {
                    document.getElementById('user-address').innerText = '';
                }
            } else {
                document.getElementById('user-address').innerText = '';
            }
        })
        .catch(() => {
            document.getElementById('user-address').innerText = '';
        });
}

// Location permission logic with button
function requestNamazLocation() {
    if (!navigator.geolocation) {
        document.getElementById('location-status').innerText = 'আপনার ব্রাউজার লোকেশন সাপোর্ট করে না।';
        return;
    }
    document.getElementById('location-status').innerText = 'আপনার অবস্থান যাচাই করা হচ্ছে...';
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('location-status').innerText = '';
        fetchPrayerTimes(pos.coords.latitude, pos.coords.longitude);
        fetchAddress(pos.coords.latitude, pos.coords.longitude);
        sessionStorage.setItem('namaz_location_permission', '1');
        document.getElementById('location-permission-btn').style.display = 'none';
    }, function() {
        // Fallback: show default (Dhaka, Bangladesh) times if permission denied
        document.getElementById('location-status').innerText = 'অবস্থান অনুমতি পাওয়া যায়নি। ঢাকা জেলার সময় দেখানো হচ্ছে।';
        fetchPrayerTimes(23.8103, 90.4125); // Dhaka
        document.getElementById('user-address').innerText = 'এলাকা: ঢাকা';
        sessionStorage.setItem('namaz_location_permission', '0');
        document.getElementById('location-permission-btn').style.display = 'block';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('location-permission-btn');
    if (btn) {
        btn.addEventListener('click', function() {
            requestNamazLocation();
        });
        btn.style.display = 'block'; // Always show button for retry
    }
    if (navigator.geolocation) {
        if (!sessionStorage.getItem('namaz_location_permission')) {
            // First load: auto request location
            requestNamazLocation();
        } else if (sessionStorage.getItem('namaz_location_permission') === '1') {
            requestNamazLocation();
        }
        // If denied, button stays for retry
    } else {
        document.getElementById('location-status').innerText = 'আপনার ব্রাউজার লোকেশন সাপোর্ট করে না।';
    }
});
</script>

<section>
    <h2>স্বাগতম!</h2>
    <p>এই ওয়েবসাইটে আপনি কুরআন, হাদীস, এবং ইসলামী ভিডিও সম্পর্কে জানতে পারবেন।</p>

    <div class="post-list">
        <?php
        include('includes/config.php');
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        // কুরআন
        if ($search) {
            $q = '%' . $conn->real_escape_string($search) . '%';
            $quran = $conn->query("SELECT * FROM quran WHERE surah LIKE '$q' OR ayat LIKE '$q' OR meaning LIKE '$q' ORDER BY id DESC");
        } else {
            $quran = $conn->query('SELECT * FROM quran ORDER BY id DESC LIMIT 3');
        }
        if ($quran && $quran->num_rows > 0) {
            echo '<div class="post-block"><div class="post-title">' . ($search ? 'অনুসন্ধানে কুরআন আয়াত' : 'সাম্প্রতিক কুরআন আয়াত') . '</div>';
            while($row = $quran->fetch_assoc()) {
                echo '<div class="post-meta">সূরা: '.htmlspecialchars($row['surah']).'</div>';
                echo '<div class="post-content">'.nl2br(htmlspecialchars($row['ayat'])).'<br><span style="color:#555">অর্থ: '.nl2br(htmlspecialchars($row['meaning'])).'</span></div><br>';
            }
            echo '</div>';
        }
        // হাদীস
        if ($search) {
            $h = '%' . $conn->real_escape_string($search) . '%';
            $hadith = $conn->query("SELECT * FROM hadith WHERE narrator LIKE '$h' OR hadith_text LIKE '$h' OR source LIKE '$h' ORDER BY id DESC");
        } else {
            $hadith = $conn->query('SELECT * FROM hadith ORDER BY id DESC LIMIT 3');
        }
        if ($hadith && $hadith->num_rows > 0) {
            echo '<div class="post-block"><div class="post-title">' . ($search ? 'অনুসন্ধানে হাদীস' : 'সাম্প্রতিক হাদীস') . '</div>';
            while($row = $hadith->fetch_assoc()) {
                echo '<div class="post-meta">বর্ণনাকারী: '.htmlspecialchars($row['narrator']).'</div>';
                echo '<div class="post-content">'.nl2br(htmlspecialchars($row['hadith_text'])).'<br><span style="color:#555">উৎস: '.htmlspecialchars($row['source']).'</span></div><br>';
            }
            echo '</div>';
        }
        // ভিডিও
        if ($search) {
            $v = '%' . $conn->real_escape_string($search) . '%';
            $videos = $conn->query("SELECT * FROM videos WHERE title LIKE '$v' OR youtube_link LIKE '$v' ORDER BY id DESC");
        } else {
            $videos = $conn->query('SELECT * FROM videos ORDER BY id DESC LIMIT 2');
        }
        if ($videos && $videos->num_rows > 0) {
            echo '<div class="post-block"><div class="post-title">' . ($search ? 'অনুসন্ধানে ইসলামিক ভিডিও' : 'সাম্প্রতিক ইসলামিক ভিডিও') . '</div>';
            while($row = $videos->fetch_assoc()) {
                $video_id = (int)$row['id'];
                echo '<div class="post-meta">'.htmlspecialchars($row['title']).'</div>';
                if (!empty($row['video_file'])) {
                    echo '<video width="350" height="200" controls style="border-radius:8px;display:block;margin-bottom:10px;">
                            <source src="' . htmlspecialchars($row['video_file']) . '" type="video/mp4">
                            আপনার ব্রাউজার ভিডিও সাপোর্ট করে না।
                        </video>';
                } elseif (!empty($row['youtube_link'])) {
                    $yt_link = $row['youtube_link'];
                    if (preg_match('/(?:v=|be\/|embed\/)([\w-]{11})/', $yt_link, $matches)) {
                        $yt_id = $matches[1];
                        echo '<iframe width="350" height="200" src="https://www.youtube.com/embed/' . htmlspecialchars($yt_id) . '" frameborder="0" allowfullscreen style="margin-bottom:10px;"></iframe>';
                    } else {
                        echo '<a href="' . htmlspecialchars($yt_link) . '" target="_blank">ভিডিও দেখুন</a>';
                    }
                } else {
                    echo '<span style="color:#c62828;">কোনো ভিডিও লিংক নেই।</span>';
                }

                // Like/Unlike buttons and count (only 1 like per user via cookie)
                $user_likes = isset($_COOKIE['video_likes']) ? explode(',', $_COOKIE['video_likes']) : [];
                $liked = in_array($video_id, $user_likes);
                echo '<div style="margin:10px 0;">';
                if (!$liked) {
                    echo '<form method="post" style="display:inline;">'
                        .'<input type="hidden" name="like_video_id" value="'.$video_id.'">'
                        .'<button type="submit" style="background:#ff9800;color:#fff;border:none;padding:6px 18px;border-radius:4px;cursor:pointer;">👍 লাইক</button>'
                    .'</form>';
                } else {
                    echo '<form method="post" style="display:inline;">'
                        .'<input type="hidden" name="unlike_video_id" value="'.$video_id.'">'
                        .'<button type="submit" style="background:#c62828;color:#fff;border:none;padding:6px 18px;border-radius:4px;cursor:pointer;">👎 আনলাইক</button>'
                    .'</form>';
                }
                // Fetch like count
                $like_count = 0;
                if (file_exists('video_likes.json')) {
                    $likes_data = json_decode(file_get_contents('video_likes.json'), true);
                    if (isset($likes_data[$video_id])) $like_count = $likes_data[$video_id];
                }
                echo ' <span style="color:#ff9800;font-weight:bold;">'.$like_count.' লাইক</span>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>