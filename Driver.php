<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>ITABookings - Tài xế</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body{font-family:Arial,sans-serif;background:#f0f2f5;margin:0;color:#333}
    .container{max-width:1400px;margin:20px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.15)}
    .header{background:linear-gradient(135deg,#003087,#0052d4);color:#fff;padding:25px 40px;display:flex;justify-content:space-between;align-items:center;font-size:28px;font-weight:bold}
    .alert{background:#ffebee;color:#c62828;padding:20px 40px;font-size:20px;font-weight:bold;text-align:center}
    .section{padding:40px}
    .title{font-size:24px;font-weight:bold;color:#003087;margin:0 0 20px}
    .filter{display:flex;gap:15px;flex-wrap:wrap;align-items:center;margin-bottom:30px}
    select,button{padding:14px 20px;border-radius:10px;border:1px solid #ddd;font-size:16px}
    button{background:#003087;color:#fff;border:none;cursor:pointer;font-weight:bold;transition:.3s}
    button:hover{background:#00205b}
    table{width:100%;border-collapse:collapse;margin-top:20px}
    th{background:#003087;color:#fff;padding:18px 15px;text-align:left;font-weight:bold}
    td{padding:16px 15px;border-bottom:1px solid #eee}
    tr:nth-child(even){background:#f8fbff}
    .link{color:#003087;font-weight:bold;text-decoration:none}
    .link:hover{text-decoration:underline}
    .total{margin:40px 0;padding:30px;background:linear-gradient(135deg,#e3f2fd,#bbdefb);border-radius:16px;text-align:center;font-size:24px}
    .total strong{color:#003087;font-size:38px}
    .charts{display:grid;grid-template-columns:1fr 1fr;gap:35px;margin-top:50px}
    .chart-box{background:#fff;padding:35px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.1);text-align:center}
    .chart-title{font-size:22px;font-weight:bold;color:#003087;margin-bottom:25px}
    canvas{height:350px!important;width:100%!important}
    #chartYear{grid-column:1/-1}
    @media(max-width:900px){.charts{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>ITABookings</div>
        <div>Xin chào tài xế <strong>Nguyễn Văn A</strong>!</div>
    </div>
    <div class="alert">
        Hiện có <strong id="pending-count">0</strong> chuyến chưa có tài xế
    </div>

    <div class="section">
        <div class="title">Chuyến đang chờ nhận</div>
        <div class="filter">
            <select id="pickup"><option value="">Tất cả điểm đón</option></select>
            <select id="dropoff"><option value="">Tất cả điểm đến</option></select>
            <button onclick="loadPending()">Lọc ngay</button>
        </div>
        <table>
            <thead><tr>
                <th>Khách hàng</th><th>Điểm đón</th><th>Điểm đến</th><th>Loại xe</th><th>Số khách</th><th>Hành động</th>
            </tr></thead>
            <tbody id="pending-body"></tbody>
        </table>
    </div>

    <div class="section">
        <div class="title">Chuyến đã nhận</div>
        <table>
            <thead><tr>
                <th>Mã đơn</th><th>Khách</th><th>Đón</th><th>Đến</th><th>Xe</th><th>Khách</th><th>Ngày nhận</th><th>Trạng thái</th>
            </tr></thead>
            <tbody id="accepted-body"></tbody>
        </table>

        <div class="total">
            Tổng cộng <strong id="total-trips">0</strong> chuyến trong <span id="period-label">tháng này</span>
        </div>

        <div class="title">Thống kê doanh thu & số chuyến</div>
        <div class="filter">
            <select id="chart-type">
                <option value="month">Tháng hiện tại</option>
                <option value="quarter">Quý hiện tại</option>
                <option value="year">Năm hiện tại</option>
            </select>
            <button onclick="loadCharts()">Cập nhật biểu đồ</button>
        </div>

        <div class="charts">
            <div class="chart-box"><div class="chart-title">Số chuyến theo tuần (tháng 11)</div><canvas id="chartMonth"></canvas></div>
            <div class="chart-box"><div class="chart-title">Số chuyến theo tháng (quý 4)</div><canvas id="chartQuarter"></canvas></div>
            <div class="chart-box"><div class="chart-title">Doanh thu theo tháng (năm 2025)</div><canvas id="chartYear"></canvas></div>
        </div>
    </div>
</div>

<script>
// Chính xác 34 tỉnh/thành bạn yêu cầu
const provinces = ["Hà Nội","Huế","Quảng Ninh","Cao Bằng","Lạng Sơn","Lai Châu","Điện Biên","Sơn La","Thanh Hóa","Nghệ An","Hà Tĩnh","Tuyên Quang","Lào Cai","Thái Nguyên","Phú Thọ","Bắc Ninh","Hưng Yên","Hải Phòng","Ninh Bình","Quảng Trị","Đà Nẵng","Quảng Ngãi","Gia Lai","Khánh Hòa","Lâm Đồng","Đắk Lắk","Thành phố Hồ Chí Minh","Đồng Nai","Tây Ninh","Cần Thơ","Vĩnh Long","Đồng Tháp","Cà Mau","An Giang"];
provinces.forEach(p => {
    ['pickup','dropoff'].forEach(id => document.getElementById(id).innerHTML += `<option value="${p}">${p}</option>`);
});

async function loadPending() {
    const pickup = document.getElementById('pickup').value;
    const dropoff = document.getElementById('dropoff').value;
    let url = 'api/get_pending.php';
    if (pickup || dropoff) url += `?pickup=${encodeURIComponent(pickup)}&dropoff=${encodeURIComponent(dropoff)}`;
    const res = await fetch(url);
    const data = await res.json();
    document.getElementById('pending-count').textContent = data.length;
    const tbody = document.getElementById('pending-body');
    tbody.innerHTML = data.length === 0 ? '<tr><td colspan="6" style="text-align:center;padding:40px;color:#999">Không có chuyến nào</td></tr>' : '';
    data.forEach(t => {
        tbody.innerHTML += `<tr>
            <td>${t.customer_name}</td>
            <td>${t.pickup_location}</td>
            <td>${t.dropoff_location}</td>
            <td>${t.vehicle_type}</td>
            <td>${t.passengers} khách</td>
            <td><a href="#" class="link" onclick="accept(${t.id});return false;">Nhận đơn ngay</a></td>
        </tr>`;
    });
}

async function loadAccepted() {
    const res = await fetch('api/get_accepted.php');
    const data = await res.json();
    const tbody = document.getElementById('accepted-body');
    tbody.innerHTML = '';
    data.forEach(t => {
        tbody.innerHTML += `<tr>
            <td>${t.order_code}</td>
            <td>${t.customer_name}</td>
            <td>${t.pickup_location}</td>
            <td>${t.dropoff_location}</td>
            <td>${t.vehicle_type}</td>
            <td>${t.passengers}</td>
            <td>${new Date(t.accept_date).toLocaleDateString('vi-VN')}</td>
            <td style="color:green;font-weight:bold">${t.status}</td>
        </tr>`;
    });
}

async function accept(id) {
    if (!confirm('Xác nhận nhận chuyến này?')) return;
    const f = new FormData(); f.append('pending_id', id);
    const r = await fetch('api/accept_trip.php', {method:'POST', body:f});
    const json = await r.json();
    alert(json.message);
    if (json.success) {
        loadPending();
        loadAccepted();
        loadCharts();
    }
}

async function loadCharts() {
    const res = await fetch('api/stats.php');
    const all = await res.json();
    const type = document.getElementById('chart-type').value;
    const data = all[type];
    const total = data.reduce((a,b) => a + parseInt(b.trips), 0);
    document.getElementById('total-trips').textContent = total;
    document.getElementById('period-label').textContent = type==='month'?'tháng này':type==='quarter'?'quý này':'năm nay';

    drawChart('chartMonth', all.month, false);
    drawChart('chartQuarter', all.quarter, false);
    drawChart('chartYear', all.year, true);
}

function drawChart(id, data, isRevenue = false) {
    const ctx = document.getElementById(id).getContext('2d');
    if (window[id]) window[id].destroy();
    window[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(x => x.label),
            datasets: [{
                label: isRevenue ? 'Doanh thu (triệu)' : 'Số chuyến',
                data: data.map(x => isRevenue ? (x.revenue/1000000).toFixed(1) : x.trips),
                backgroundColor: '#003087',
                borderRadius: 10,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => isRevenue ? `Doanh thu: ${(ctx.parsed.y*1000000).toLocaleString('vi-VN')} ₫` : `Số chuyến: ${ctx.parsed.y}`
                    }
                }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
}

window.onload = () => {
    loadPending();
    loadAccepted();
    loadCharts();
};
</script>
<script>
// Fix lỗi Chart.js đôi khi không vẽ nếu canvas chưa sẵn sàng
setTimeout(() => {
    loadCharts();
}, 500);
</script>
</body>
</html>