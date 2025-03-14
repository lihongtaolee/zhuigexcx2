<template>
  <view class="result-page">
    <view class="share-icon" @tap="goToShare">
      <image src="https://img.icons8.com/fluency/48/share-rounded.png" mode="aspectFit"></image>
    </view>
    
    <view class="result-container">
      <!-- 上部分 -->
      <view class="result-header">
        <text class="result-title">评测结果</text>
        <view class="score-section">
          <view class="score-circle">
            <text class="score-value">{{ totalScore }}</text>
            <text class="score-label">综合评分</text>
          </view>
        </view>
        
        <view class="radar-chart-container">
          <canvas canvas-id="radarChart" id="radarChart" class="radar-chart"></canvas>
          <view class="chart-legend">
            <view class="legend-item">
              <view class="legend-color" style="background-color: rgba(255, 107, 129, 0.5);"></view>
              <text class="legend-text">您的宝宝</text>
            </view>
            <view class="legend-item">
              <view class="legend-color" style="background-color: rgba(200, 200, 200, 0.5);"></view>
              <text class="legend-text">标准水平</text>
            </view>
          </view>
        </view>
      </view>
      
      <!-- 中间区域：服务转化 -->
      <view class="service-section">
        <view class="service-card">
          <view class="service-icon">
            <image src="https://img.icons8.com/color/96/microscope.png" mode="aspectFit"></image>
          </view>
          <view class="service-content">
            <text class="service-title">继续精准评测</text>
            <text class="service-desc">获取更详细的身高发育分析和个性化成长方案</text>
          </view>
          <button class="service-btn" @tap="startPreciseAssessment">立即开始</button>
        </view>
        
        <view class="service-card">
          <view class="service-icon">
            <image src="https://img.icons8.com/color/96/add-user-group-woman-man.png" mode="aspectFit"></image>
          </view>
          <view class="service-content">
            <text class="service-title">邀请好友评测</text>
            <text class="service-desc">分享给亲友，共同关注宝宝健康成长</text>
          </view>
          <button class="service-btn" @tap="goToShare">立即分享</button>
        </view>
      </view>
      
      <!-- 下方区域：详细评测结果 -->
      <view class="detailed-result">
        <text class="detailed-title">详细评测报告</text>
        
        <view class="result-tabs">
          <view 
            v-for="(dimension, index) in dimensions" 
            :key="index"
            class="tab" 
            :class="{ active: activeTabIndex === index }"
            @tap="switchTab(index)"
          >
            {{ dimension.name }}
          </view>
        </view>
        
        <view class="tab-content">
          <view v-if="activeTabIndex === 0" class="tab-pane">
            <view class="dimension-score">
              <text class="score">{{ dimensions[0].score }}</text>
              <text class="label">分</text>
            </view>
            <text class="dimension-title">遗传因素评估</text>
            <text class="dimension-desc">根据您提供的信息，您的宝宝在遗传因素方面具有较好的身高发育潜力。父母身高处于中等偏上水平，家族中也有较高身高的成员。</text>
            <text class="sub-title">优势：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">父母身高均处于平均水平以上</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">家族中有身高优势的成员</text>
              </view>
            </view>
            <text class="sub-title">建议：</text>
            <text class="dimension-desc">虽然遗传因素对身高有重要影响，但环境因素同样不可忽视。建议您：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">关注宝宝的营养均衡，确保摄入足够的蛋白质和钙质</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">鼓励宝宝进行适量的户外活动，促进骨骼发育</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">保证充足的睡眠时间，有利于生长激素的分泌</text>
              </view>
            </view>
          </view>
          
          <view v-if="activeTabIndex === 1" class="tab-pane">
            <!-- 营养状况评估内容 -->
            <view class="dimension-score">
              <text class="score">{{ dimensions[1].score }}</text>
              <text class="label">分</text>
            </view>
            <text class="dimension-title">营养状况评估</text>
            <text class="dimension-desc">您的宝宝在营养摄入方面表现良好，但仍有提升空间。饮食结构基本合理，但某些关键营养素的摄入可能不足。</text>
            <text class="sub-title">优势：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">饮食种类丰富多样</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">蛋白质摄入充足</text>
              </view>
            </view>
            <text class="sub-title">不足：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">钙质和维生素D摄入可能不足</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">膳食纤维摄入偏低</text>
              </view>
            </view>
            <text class="sub-title">建议：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">增加奶制品的摄入，保证每日钙质需求</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">适当增加户外活动时间，促进维生素D的合成</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">多食用全谷物、新鲜蔬果，增加膳食纤维摄入</text>
              </view>
            </view>
          </view>
          
          <view v-if="activeTabIndex === 2" class="tab-pane">
            <!-- 运动习惯评估内容 -->
            <view class="dimension-score">
              <text class="score">{{ dimensions[2].score }}</text>
              <text class="label">分</text>
            </view>
            <text class="dimension-title">运动习惯评估</text>
            <text class="dimension-desc">您的宝宝在运动方面有一定基础，但运动频率和强度可能不够理想。适当的运动对促进骨骼发育和身高增长有重要作用。</text>
            <text class="sub-title">优势：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">有参与一些体育活动的兴趣</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">基本运动技能发展正常</text>
              </view>
            </view>
            <text class="sub-title">不足：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">每日活动时间不足</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">缺乏系统性的运动计划</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">跳跃等拉伸骨骼的运动较少</text>
              </view>
            </view>
            <text class="sub-title">建议：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">每天保证至少1小时的中高强度身体活动</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">增加跳绳、篮球等有助于身高增长的运动</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">培养定期运动的习惯，可以选择宝宝感兴趣的运动项目</text>
              </view>
            </view>
          </view>
          
          <view v-if="activeTabIndex === 3" class="tab-pane">
            <!-- 睡眠质量评估内容 -->
            <view class="dimension-score">
              <text class="score">{{ dimensions[3].score }}</text>
              <text class="label">分</text>
            </view>
            <text class="dimension-title">睡眠质量评估</text>
            <text class="dimension-desc">您的宝宝睡眠时间基本达标，但睡眠质量可能有待提高。良好的睡眠对生长激素的分泌至关重要，是促进身高增长的关键因素。</text>
            <text class="sub-title">优势：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">睡眠时间基本符合年龄要求</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">有固定的睡眠时间</text>
              </view>
            </view>
            <text class="sub-title">不足：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">入睡时间较晚</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">睡眠中可能有频繁醒来的情况</text>
              </view>
            </view>
            <text class="sub-title">建议：</text>
            <view class="bullet-list">
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">保持规律的作息时间，尽量在晚上9点前入睡</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">睡前1小时避免使用电子设备，减少蓝光对睡眠的影响</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">创造安静、舒适的睡眠环境，提高睡眠质量</text>
              </view>
              <view class="bullet-item">
                <text class="bullet">•</text>
                <text class="bullet-text">睡前可以进行轻松的活动，如阅读故事书、听轻音乐等</text>
              </view>
            </view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  data() {
    return {
      dimensions: [
        { name: '遗传因素', score: 85 },
        { name: '营养状况', score: 78 },
        { name: '运动习惯', score: 82 },
        { name: '睡眠质量', score: 75 }
      ],
      activeTabIndex: 0,
      totalScore: 85,
      animationTimer: null,
      animationFrame: 0,
      maxAnimationFrames: 30,
      animationProgress: 0
    }
  },
  onReady() {
    // 延迟一下再开始绘制，确保canvas已经准备好
    setTimeout(() => {
      this.startRadarChartAnimation();
    }, 300);
  },
  onUnload() {
    // 清除动画定时器
    if (this.animationTimer) {
      clearTimeout(this.animationTimer);
    }
  },
  methods: {
    startRadarChartAnimation() {
      // 重置动画状态
      this.animationFrame = 0;
      this.animationProgress = 0;
      
      // 开始动画循环
      this.animateRadarChart();
    },
    
    animateRadarChart() {
      // 增加动画帧
      this.animationFrame++;
      
      // 计算动画进度（使用缓动函数使动画更自然）
      this.animationProgress = this.easeOutQuad(this.animationFrame / this.maxAnimationFrames);
      
      // 绘制当前帧
      this.drawRadarChartFrame();
      
      // 如果动画未完成，继续下一帧
      if (this.animationFrame < this.maxAnimationFrames) {
        this.animationTimer = setTimeout(() => {
          this.animateRadarChart();
        }, 16); // 约60fps
      }
    },
    
    // 缓动函数，使动画更自然
    easeOutQuad(t) {
      return t < 1 ? t * t : 1;
    },
    
    drawRadarChartFrame() {
      try {
        const ctx = uni.createCanvasContext('radarChart', this);
        
        // 设置雷达图的中心点和半径
        const centerX = 150;
        const centerY = 180; // 将中心点稍微下移，使图表在垂直方向上居中
        const radius = 120;
        
        // 清空画布
        ctx.clearRect(0, 0, 300, 360);
        
        // 绘制雷达图的背景网格和刻度
        this.drawRadarGrid(ctx, centerX, centerY, radius);
        
        // 根据动画进度绘制数据
        const standardData = [75, 75, 75, 75];
        const babyData = [
          this.dimensions[0].score * this.animationProgress,
          this.dimensions[1].score * this.animationProgress,
          this.dimensions[2].score * this.animationProgress,
          this.dimensions[3].score * this.animationProgress
        ];
        
        // 先绘制标准水平
        this.drawRadarData(ctx, centerX, centerY, radius, standardData, 'rgba(100, 180, 255, 0.2)', 'rgba(100, 180, 255, 1)');
        
        // 再绘制宝宝数据
        this.drawRadarData(ctx, centerX, centerY, radius, babyData, 'rgba(255, 107, 129, 0.2)', 'rgba(255, 107, 129, 1)');
        
        ctx.draw();
      } catch (error) {
        console.error('绘制雷达图失败:', error);
      }
    },
    
    drawRadarGrid(ctx, centerX, centerY, radius) {
      const sides = 4; // 四个维度
      const angleStep = (Math.PI * 2) / sides;
      
      // 绘制背景
      ctx.beginPath();
      ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
      ctx.setFillStyle('rgba(240, 240, 250, 0.2)');
      ctx.fill();
      
      // 绘制同心圆和刻度
      const scaleValues = [20, 40, 60, 80, 100];
      for (let i = 0; i < scaleValues.length; i++) {
        const r = (radius * scaleValues[i]) / 100;
        
        // 绘制同心菱形
        ctx.beginPath();
        for (let j = 0; j < sides; j++) {
          // 从上方开始，顺时针方向
          const angle = j * angleStep; // 从正上方开始
          const x = centerX + r * Math.cos(angle);
          const y = centerY + r * Math.sin(angle);
          
          if (j === 0) {
            ctx.moveTo(x, y);
          } else {
            ctx.lineTo(x, y);
          }
        }
        ctx.closePath();
        ctx.setStrokeStyle('rgba(200, 200, 200, 0.5)');
        ctx.stroke();
        
        // 绘制刻度值（只在上方绘制）
        if (i === scaleValues.length - 1 || i === 3 || i === 2 || i === 1 || i === 0) {
          // 只在上方中心线上绘制刻度值
          const scaleY = centerY - (radius * scaleValues[i]) / 100;
          ctx.setFontSize(12);
          ctx.setTextAlign('center');
          ctx.setTextBaseline('bottom');
          ctx.setFillStyle('#666');
          
          // 只有不是0的刻度才显示
          if (scaleValues[i] > 0) {
            ctx.fillText(scaleValues[i].toString(), centerX, scaleY - 5);
          }
        }
      }
      
      // 绘制从中心到各顶点的线
      for (let i = 0; i < sides; i++) {
        const angle = i * angleStep; // 从正上方开始
        const x = centerX + radius * Math.cos(angle);
        const y = centerY + radius * Math.sin(angle);
        
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(x, y);
        ctx.setStrokeStyle('rgba(200, 200, 200, 0.5)');
        ctx.stroke();
        
        // 绘制维度标签，根据角度调整位置
        let labelX, labelY;
        const labelDistance = radius + 30;
        
        // 根据不同位置调整标签位置和文本
        let dimensionName = '';
        if (i === 0) { // 上方 - 遗传因素
          dimensionName = '遗传因素';
          labelX = centerX;
          labelY = centerY - labelDistance;
          ctx.setTextAlign('center');
          ctx.setTextBaseline('bottom');
        } else if (i === 1) { // 右侧 - 营养状况
          dimensionName = '营养状况';
          labelX = centerX + labelDistance;
          labelY = centerY;
          ctx.setTextAlign('left');
          ctx.setTextBaseline('middle');
        } else if (i === 2) { // 下方 - 运动习惯
          dimensionName = '运动习惯';
          labelX = centerX;
          labelY = centerY + labelDistance;
          ctx.setTextAlign('center');
          ctx.setTextBaseline('top');
        } else { // 左侧 - 睡眠质量
          dimensionName = '睡眠质量';
          labelX = centerX - labelDistance;
          labelY = centerY;
          ctx.setTextAlign('right');
          ctx.setTextBaseline('middle');
        }
        
        ctx.setFontSize(14);
        ctx.setFillStyle('#666');
        ctx.fillText(dimensionName, labelX, labelY);
      }
    },
    
    drawRadarData(ctx, centerX, centerY, radius, data, fillColor, strokeColor) {
      const sides = 4;
      const angleStep = (Math.PI * 2) / sides;
      
      // 重新排列数据以匹配新的维度顺序：上-遗传因素，右-营养状况，下-运动习惯，左-睡眠质量
      const orderedData = [
        data[0], // 遗传因素 - 上
        data[1], // 营养状况 - 右
        data[2], // 运动习惯 - 下
        data[3]  // 睡眠质量 - 左
      ];
      
      // 绘制数据区域
      ctx.beginPath();
      for (let i = 0; i < sides; i++) {
        const angle = i * angleStep; // 从正上方开始
        const value = orderedData[i] / 100; // 假设满分是100
        const x = centerX + radius * value * Math.cos(angle);
        const y = centerY + radius * value * Math.sin(angle);
        
        if (i === 0) {
          ctx.moveTo(x, y);
        } else {
          ctx.lineTo(x, y);
        }
      }
      ctx.closePath();
      ctx.setFillStyle(fillColor);
      ctx.fill();
      ctx.setStrokeStyle(strokeColor);
      ctx.setLineWidth(2);
      ctx.stroke();
      
      // 绘制数据点和数值
      for (let i = 0; i < sides; i++) {
        const angle = i * angleStep; // 从正上方开始
        const value = orderedData[i] / 100;
        const x = centerX + radius * value * Math.cos(angle);
        const y = centerY + radius * value * Math.sin(angle);
        
        // 绘制数据点
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, Math.PI * 2);
        ctx.setFillStyle(strokeColor);
        ctx.fill();
        
        // 如果是宝宝数据且动画完成，显示数值
        if (fillColor.includes('255, 107, 129') && this.animationProgress >= 0.99) {
          // 根据不同位置调整数值标签位置
          let valueX, valueY;
          const valueDistance = 15;
          
          if (i === 0) { // 上方 - 遗传因素
            valueX = x;
            valueY = y - valueDistance;
            ctx.setTextAlign('center');
            ctx.setTextBaseline('bottom');
          } else if (i === 1) { // 右侧 - 营养状况
            valueX = x + valueDistance;
            valueY = y;
            ctx.setTextAlign('left');
            ctx.setTextBaseline('middle');
          } else if (i === 2) { // 下方 - 运动习惯
            valueX = x;
            valueY = y + valueDistance;
            ctx.setTextAlign('center');
            ctx.setTextBaseline('top');
          } else { // 左侧 - 睡眠质量
            valueX = x - valueDistance;
            valueY = y;
            ctx.setTextAlign('right');
            ctx.setTextBaseline('middle');
          }
          
          ctx.setFontSize(12);
          ctx.setFillStyle(strokeColor);
          ctx.fillText(Math.round(orderedData[i]).toString(), valueX, valueY);
        }
      }
    },
    
    startPreciseAssessment() {
      // 实现继续精准评测的逻辑
      uni.showToast({
        title: '即将开始精准评测',
        icon: 'none'
      });
    },
    
    goToShare() {
      uni.navigateTo({
        url: './share'
      });
    },
    
    switchTab(index) {
      this.activeTabIndex = index;
    }
  }
}
</script>

<style>
.result-page {
  background-color: #fff;
  min-height: 100vh;
  position: relative;
}

.share-icon {
  position: fixed;
  top: 40rpx;
  right: 40rpx;
  z-index: 100;
  background-color: #fff;
  border-radius: 50%;
  width: 100rpx;
  height: 100rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.1);
}

.share-icon image {
  width: 60rpx;
  height: 60rpx;
}

.result-container {
  padding: 30rpx;
  padding-top: 100rpx;
  max-width: 750rpx;
  margin: 0 auto;
}

.result-header {
  margin-bottom: 40rpx;
}

.result-title {
  font-size: 46rpx;
  font-weight: bold;
  color: #ff6b81;
  display: block;
  margin-bottom: 30rpx;
  text-align: center;
}

.score-section {
  margin-bottom: 40rpx;
  display: flex;
  justify-content: center;
}

.score-circle {
  background-color: #ff6b81;
  border-radius: 50%;
  width: 220rpx;
  height: 220rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.score-value {
  font-size: 80rpx;
  font-weight: bold;
  color: #fff;
  line-height: 1;
  margin-bottom: 10rpx;
}

.score-label {
  font-size: 28rpx;
  color: #fff;
  display: block;
}

.radar-chart-container {
  margin-bottom: 80rpx; /* 增加底部间距，确保与图例保持适当距离 */
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  position: relative;
}

.radar-chart {
  width: 100%;
  height: 500rpx; /* 调整高度 */
  display: flex;
  justify-content: center;
  margin-top: 20rpx; /* 增加顶部间距 */
}

.chart-legend {
  display: flex;
  justify-content: center;
  gap: 40rpx;
  margin-top: 30rpx; /* 调整图例与雷达图的间距 */
  margin-bottom: 20rpx;
  position: relative;
  z-index: 10;
}

.legend-item {
  display: flex;
  align-items: center;
  background-color: #fff;
  padding: 6rpx 16rpx;
  border-radius: 30rpx;
  box-shadow: 0 2rpx 8rpx rgba(0, 0, 0, 0.1);
}

.legend-color {
  width: 24rpx;
  height: 24rpx;
  border-radius: 4rpx; /* 改为方形色块 */
  margin-right: 10rpx;
}

.legend-item:first-child .legend-color {
  background-color: rgba(255, 107, 129, 0.8) !important; /* 确保颜色正确 */
}

.legend-item:last-child .legend-color {
  background-color: rgba(100, 180, 255, 0.8) !important; /* 确保颜色正确 */
}

.legend-text {
  font-size: 24rpx;
  color: #666;
}

.service-section {
  display: flex;
  flex-direction: column;
  gap: 30rpx;
  margin-bottom: 60rpx;
  align-items: center;
  width: 100%;
}

.service-card {
  background-color: #F8F8F8;
  border-radius: 20rpx;
  padding: 40rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  box-sizing: border-box;
}

.service-icon {
  margin-bottom: 30rpx;
}

.service-icon image {
  width: 120rpx;
  height: 120rpx;
}

.service-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #ff6b81;
  margin-bottom: 16rpx;
  display: block;
  text-align: center;
}

.service-desc {
  font-size: 28rpx;
  color: #666;
  text-align: center;
  margin-bottom: 30rpx;
  display: block;
}

.service-btn {
  background-color: #ff6b81;
  color: white;
  font-size: 32rpx;
  padding: 16rpx 60rpx;
  border-radius: 40rpx;
  font-weight: bold;
}

.detailed-result {
  background-color: #F8F8F8;
  border-radius: 20rpx;
  padding: 40rpx;
}

.detailed-title {
  font-size: 40rpx;
  font-weight: bold;
  color: #ff6b81;
  text-align: center;
  margin-bottom: 40rpx;
  display: block;
}

.result-tabs {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20rpx;
  border-bottom: 1px solid #eee;
}

.tab {
  flex: 1;
  text-align: center;
  padding: 20rpx 0;
  font-size: 30rpx;
  color: #666;
  position: relative;
  transition: all 0.3s ease;
}

.tab.active {
  color: #ff6b81;
  font-weight: bold;
}

.tab.active::after {
  content: '';
  position: absolute;
  bottom: -2rpx;
  left: 50%;
  transform: translateX(-50%);
  width: 80rpx;
  height: 6rpx;
  background-color: #ff6b81;
  border-radius: 3rpx;
  transition: all 0.3s ease;
}

.tab-content {
  background-color: #fff;
  border-radius: 16rpx;
  padding: 40rpx;
  margin-top: 30rpx;
}

.tab-pane {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  text-align: left;
}

.dimension-score {
  display: flex;
  align-items: baseline;
  justify-content: center;
  margin-bottom: 30rpx;
  width: 100%;
  text-align: center;
}

.score {
  font-size: 70rpx;
  font-weight: bold;
  color: #ff6b81;
  margin-right: 10rpx;
  line-height: 1;
}

.label {
  font-size: 32rpx;
  color: #666;
}

.dimension-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 20rpx;
  display: block;
  width: 100%;
  text-align: left;
}

.dimension-desc {
  font-size: 30rpx;
  color: #666;
  margin-bottom: 20rpx;
  display: block;
  line-height: 1.6;
  width: 100%;
  text-align: left;
}

.sub-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #ff6b81;
  margin: 30rpx 0 16rpx;
  display: block;
  width: 100%;
  text-align: left;
}

.bullet-list {
  margin-bottom: 20rpx;
  width: 100%;
  text-align: left;
}

.bullet-item {
  display: flex;
  margin-bottom: 16rpx;
  width: 100%;
  text-align: left;
}

.bullet {
  font-size: 32rpx;
  color: #ff6b81;
  margin-right: 16rpx;
  flex-shrink: 0;
}

.bullet-text {
  font-size: 30rpx;
  color: #666;
  line-height: 1.6;
}
</style> 