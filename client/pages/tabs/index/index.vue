<template>
	<view class="content">
	  <!-- 搜索顶部模块 -->
	  <uni-nav-bar :fixed="true" :statusBar="true" :border="false" background-color="#0863cc">
		<view class="zhuige-top-bar" :style="style">
		  <template v-slot:left>
			<view class="zhuige-top-logo">
			  <image v-if="logo" mode="heightFix" :src="logo"></image>
			  <view v-else class="logo-placeholder"></view>
			</view>
		  </template>
		  <view class="zhuige-top-search" @click="openLink('/pages/base/search/search')">
			<uni-icons type="search" color="#999999" size="18"></uni-icons>
			<text>关键词...</text>
		  </view>
		</view>
	  </uni-nav-bar>
	
	  <!-- 身高预测AI模块容器 -->
	  <view class="zhuige-wide-box height-predict-wrapper">
		<zhuige-height-predict
		  :currentHeight.sync="heightData.currentHeight"
		  :geneticHeight.sync="heightData.geneticHeight"
		  :targetHeight.sync="heightData.targetHeight"
		  :probability.sync="heightData.probability"
		  :baobaoname.sync="heightData.baobaoname"
		  :updateTime.sync="heightData.updateTime"
		/>
	  </view>

	  <!-- 金刚位模块 -->
	  <view class="zhuige-wide-box icons-wrapper">
	    <view class="icons-container">
	      <zhuige-icons v-if="moduleStatus.icons === 'show' && icons && icons.length > 0" :items="icons" />
	      <view v-else-if="moduleStatus.icons === 'loading'" class="skeleton-icons"></view>
	    </view>
	  </view>
	  
	  <!-- 身高专题模块 -->
	  <view class="zhuige-wide-box">
	    <transition name="fade">
	      <view v-if="moduleStatus.sgztmk === 'show' && sgztmkModules && sgztmkModules.length > 0">
	        <zhuige-sgztmk-module
	          v-for="(module, index) in sgztmkModules"
	          :key="module.id"
	          :moduleTitle="module.title"
	          :leftModule="{
	            title: module.left_module.title,
	            icon: module.icon,
	            description: module.left_module.description,
	            image: module.left_module.image,
	            buttonText: module.left_module.button_text,
	            link: module.left_module.link,
	            value: module.left_module.value,
	            bgColor: module.left_module.bg_color
	          }"
	          :rightTopModule="{
	            title: module.right_top_module.title,
	            description: module.right_top_module.description,
	            image: module.right_top_module.image,
	            buttonText: module.right_top_module.button_text,
	            link: module.right_top_module.link,
	            bgColor: module.right_top_module.bg_color
	          }"
	          :rightBottomModule="{
	            title: module.right_bottom_module.title,
	            description: module.right_bottom_module.description,
	            image: module.right_bottom_module.image,
	            buttonText: module.right_bottom_module.button_text,
	            link: module.right_bottom_module.link,
	            bgColor: module.right_bottom_module.bg_color
	          }"
	        />
	      </view>
	      <view v-else-if="moduleStatus.sgztmk === 'loading'" class="skeleton-sgztmk"></view>
	    </transition>
	  </view>
	
	  <!-- 底部提示文本 -->
	  <view class="bottom-tip">
		<text>哎呀，滑到底啦！宝宝要长高高~</text>
	  </view>
	</view>
</template>
  
<script>
import Util from '@/utils/util';
import Api from '@/utils/api';
import Rest from '@/utils/rest';

import ZhuigeIcons from "@/components/zhuige-icons";
import zhuigeHeightPredict from '@/components/zhuige-height-predict.vue';
import ZhuigeSgztmkModule from "@/components/zhuige-sgztmk-module.vue";

export default {
	components: {
	  ZhuigeIcons,
	  zhuigeHeightPredict,
	  ZhuigeSgztmkModule
	},
	data() {
	  return {
		isLoading: true,
		logo: undefined,
		style: '',
		icons: [],
		heightData: this.getDefaultHeightData(),
		apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige/sgtool',
		sgztmkModules: [],
		isModulesLoading: true,
		logoCacheKey: 'zhuige_xcx_logo_cache',
		logoCacheTime: 'zhuige_xcx_logo_cache_time',
		// 新增模块状态管理
		moduleStatus: {
		  icons: 'loading',
		  sgztmk: 'loading'
		},
		// 新增本地缓存键
		iconsCacheKey: 'zhuige_xcx_icons_cache',
		iconsCacheTime: 'zhuige_xcx_icons_cache_time'
	  }
	},
	onLoad() {
	  // 先使用预加载的缓存数据立即渲染
	  this.loadCachedData();
	  // 然后再异步加载最新数据
	  this.refresh();
	  this.fetchUserHeightData();
	},
	onShow() {
	  // 先使用预加载的缓存数据立即渲染
	  this.loadCachedData();
	  // 然后再异步加载最新数据
	  this.refresh();
	  const auth = uni.getStorageSync('zhuige_xcx_user');
	  if (auth && auth.user_id) {
		this.fetchUserHeightData();
	  } else {
		this.heightData = this.getDefaultHeightData();
	  }
	},
	methods: {
	  openLink(link) {
		Util.openLink(link);
	  },
	  refresh() {
		this.loadSetting();
	  },
	  loadCachedData() {
		// 尝试从全局缓存加载首页数据
		const app = getApp();
		if (app.globalData.homePageCache) {
		  // 加载首页基本数据
		  this.logo = app.globalData.homePageCache.logo;
		  
		  // 更新金刚位模块状态和数据
		  if (app.globalData.homePageCache.icons && app.globalData.homePageCache.icons.length > 0) {
		    this.icons = app.globalData.homePageCache.icons;
		    this.moduleStatus.icons = 'show';
		  } else {
		    // 尝试从本地缓存加载金刚位数据
		    this.loadCachedIcons();
		  }
		  
		  if (app.globalData.homePageCache.style) {
			this.style = app.globalData.homePageCache.style;
		  }
		  this.isLoading = false;
		} else {
		  // 如果没有全局缓存，尝试从本地缓存加载logo和金刚位
		  this.loadCachedLogo();
		  this.loadCachedIcons();
		}
		
		// 尝试从全局缓存加载身高专题模块数据
		if (app.globalData.sgztmkModulesCache) {
		  this.sgztmkModules = app.globalData.sgztmkModulesCache;
		  this.moduleStatus.sgztmk = this.sgztmkModules.length > 0 ? 'show' : 'hide';
		  this.isModulesLoading = false;
		} else {
		  // 尝试从本地缓存加载身高专题模块数据
		  try {
		    const cachedModules = uni.getStorageSync('zhuige_sgztmk_modules_cache');
		    const cacheTime = uni.getStorageSync('zhuige_sgztmk_modules_cache_time');
		    const now = new Date().getTime();
		    
		    // 如果有缓存且未过期（24小时有效期）
		    if (cachedModules && cacheTime && (now - cacheTime < 24 * 60 * 60 * 1000)) {
		      this.sgztmkModules = JSON.parse(cachedModules);
		      this.moduleStatus.sgztmk = this.sgztmkModules.length > 0 ? 'show' : 'hide';
		      this.isModulesLoading = false;
		    }
		  } catch (e) {
		    console.error('读取身高专题模块缓存失败', e);
		  }
		}
	  },
	  
	  loadCachedLogo() {
		// 尝试从缓存加载logo
		const cachedLogo = uni.getStorageSync(this.logoCacheKey);
		const cacheTime = uni.getStorageSync(this.logoCacheTime);
		const now = new Date().getTime();
		
		// 如果有缓存且未过期（24小时有效期）
		if (cachedLogo && cacheTime && (now - cacheTime < 24 * 60 * 60 * 1000)) {
		  this.logo = cachedLogo;
		  this.isLoading = false;
		}
	  },
	  
	  loadCachedIcons() {
	    // 尝试从缓存加载金刚位数据
	    const cachedIcons = uni.getStorageSync(this.iconsCacheKey);
	    const cacheTime = uni.getStorageSync(this.iconsCacheTime);
	    const now = new Date().getTime();
	    
	    // 如果有缓存且未过期（24小时有效期）
	    if (cachedIcons && cacheTime && (now - cacheTime < 24 * 60 * 60 * 1000)) {
	      try {
	        const parsedIcons = JSON.parse(cachedIcons);
	        if (Array.isArray(parsedIcons) && parsedIcons.length > 0) {
	          this.icons = parsedIcons;
	          this.moduleStatus.icons = 'show';
	        } else {
	          this.moduleStatus.icons = 'hide';
	        }
	      } catch (e) {
	        console.error('解析金刚位缓存数据失败', e);
	        this.moduleStatus.icons = 'loading';
	      }
	    }
	  },

	  loadSetting() {
		Rest.post(Api.URL('setting', 'home'))
		  .then(res => {
		    // 更新logo
			this.logo = res.data.logo;
			
			// 更新金刚位模块
			const newIcons = res.data.icons || [];
			
			// 只有当数据真正变化时才更新视图
			const currentIconsStr = JSON.stringify(this.icons);
			const newIconsStr = JSON.stringify(newIcons);
			
			if (currentIconsStr !== newIconsStr) {
			  // 如果当前没有数据但新数据有，或者数据确实变化了
			  if (this.icons.length === 0 || currentIconsStr !== newIconsStr) {
			    this.icons = newIcons;
			  }
			}
			
			this.moduleStatus.icons = newIcons.length > 0 ? 'show' : 'hide';
			
			if (res.data.style) {
			  this.style = res.data.style;
			}
			
			// 更新本地缓存
			uni.setStorageSync(this.logoCacheKey, res.data.logo);
			uni.setStorageSync(this.logoCacheTime, new Date().getTime());
			
			// 缓存金刚位数据
			uni.setStorageSync(this.iconsCacheKey, JSON.stringify(newIcons));
			uni.setStorageSync(this.iconsCacheTime, new Date().getTime());
			
			// 更新全局缓存
			const app = getApp();
			if (app.globalData.homePageCache) {
			  app.globalData.homePageCache.logo = res.data.logo;
			  app.globalData.homePageCache.icons = res.data.icons;
			  app.globalData.homePageCache.style = res.data.style;
			  app.globalData.homePageCache.timestamp = new Date().getTime();
			}
			
			this.fetchSgztmkModules();
			this.isLoading = false;
		  })
		  .catch(err => {
			this.isLoading = false;
		  });
	  },
	  getDefaultHeightData() {
		return {
		  currentHeight: 165,
		  geneticHeight: 175,
		  targetHeight: 180,
		  probability: 85,
		  baobaoname: '演示宝宝',
		  updateTime: ''
		};
	  },
	  async fetchUserHeightData() {
		const auth = uni.getStorageSync('zhuige_xcx_user');
		
		if (!auth || !auth.user_id) {
		  this.heightData = this.getDefaultHeightData();
		  this.isLoading = false;
		  return;
		}
		
		try {
		  const res = await uni.request({
			url: `${this.apiBaseUrl}/get_user_height_data`,
			method: 'GET',
			data: { user_id: auth.user_id },
			header: { 'content-type': 'application/json' }
		  });
  
		  if (res.statusCode === 200 && res.data && res.data.code === 0) {
			const data = res.data.data;
			
			if (data && data.baobaoname && data.current_height && data.target_height && data.prediction_probability) {
			  this.heightData = {
				geneticHeight: Number(data.gender === 1 ? data.boy_genetic_height : data.girl_genetic_height),
				currentHeight: Number(data.current_height),
				targetHeight: Number(data.target_height),
				probability: Number(data.prediction_probability),
				baobaoname: data.baobaoname,
				updateTime: data.create_time
			  };
			} else {
			  this.heightData = this.getDefaultHeightData();
			}
		  }
		} catch (err) {
		  uni.showToast({ title: '获取数据失败', icon: 'none' });
		  this.heightData = this.getDefaultHeightData();
		} finally {
		  this.isLoading = false;
		}
	  },
	  async fetchSgztmkModules() {
		// 不重置加载状态，保持之前的状态，避免闪烁
		try {
		  const res = await Rest.post(Api.URL('sgtool', 'get_sgztmk_modules'));
		  if (res.data && Array.isArray(res.data.modules)) {
			const auth = uni.getStorageSync('zhuige_xcx_user');
			
			const newModules = await Promise.all(res.data.modules.map(async module => {
			  if (module.left_module.value_api) {
				let value = '60';
				
				if (auth && auth.user_id) {
				  try {
					const apiUrl = module.left_module.value_api.replace('{user_id}', auth.user_id);
					
					const response = await uni.request({
					  url: apiUrl,
					  method: 'GET',
					  header: { 'content-type': 'application/json' }
					});
					
					if (response.data && response.data.code === 200 && response.data.data) {
					  const result = response.data.data;
					  if (Array.isArray(result) && result.length > 0) {
						const latestRecord = result[result.length - 1];
						const queryString = apiUrl.split('?')[1];
						const fieldsMatch = queryString.match(/fields=([^&]*)/);
						const fields = fieldsMatch ? fieldsMatch[1] : null;
						
						if (fields && latestRecord[fields] !== undefined) {
						  value = Number(latestRecord[fields]).toFixed(1);
						}
					  }
					}
				  } catch (error) {}
				}
				
				module.left_module.value = value;
			  }
			  return module;
			}));
			
			// 只有当数据真正变化时才更新视图
			if (JSON.stringify(this.sgztmkModules) !== JSON.stringify(newModules)) {
			  this.sgztmkModules = newModules;
			}
			
			// 更新模块状态
			this.moduleStatus.sgztmk = newModules.length > 0 ? 'show' : 'hide';
			
			// 更新全局缓存
			const app = getApp();
			if (app.globalData) {
			  app.globalData.sgztmkModulesCache = this.sgztmkModules;
			  
			  // 同时更新本地缓存
			  try {
				uni.setStorageSync('zhuige_sgztmk_modules_cache', JSON.stringify(this.sgztmkModules));
				uni.setStorageSync('zhuige_sgztmk_modules_cache_time', new Date().getTime());
			  } catch (e) {
				console.error('缓存身高专题模块数据失败', e);
			  }
			}
		  } else {
			this.sgztmkModules = [];
			this.moduleStatus.sgztmk = 'hide';
		  }
		} catch (err) {
		  this.sgztmkModules = [];
		  this.moduleStatus.sgztmk = 'hide';
		} finally {
		  this.isModulesLoading = false;
		}
	  }
	}
}
</script>
  
<style lang="scss">
.zhuige-top-logo {
	display: flex;
	align-items: center;
	margin-right: 15rpx;
	height: 48rpx;
	width: 128rpx;

	image {
	  height: 48rpx;
	  width: 128rpx;
	}

	.logo-placeholder {
	  height: 48rpx;
	  width: 128rpx;
	  background-color: rgba(255, 255, 255, 0.2);
	  border-radius: 6rpx;
	}
}

.zhuige-top-search {
	display: flex;
	align-items: center;
	width: 80%;
	height: 32px;
	padding-left: 20rpx;
	color: #999999;
	font-size: 28rpx;
	border: 1rpx solid #999999;
	border-radius: 16rpx;
}

.zhuige-wide-box {
	padding: 0 20rpx;
	margin-bottom: 20rpx;
}

/* 合并了重复定义，移除了 !important */
.height-predict-wrapper {
	padding: 0;
	margin: 20rpx 0;
}

/* 修改金刚位模块的底部外边距 */
.icons-wrapper {
	min-height: 200rpx;
	margin-bottom: 0; /* 覆盖通用的20rpx外边距 */
}

.icons-container {
  position: relative;
  min-height: 200rpx;
  width: 100%;
}

/* 添加过渡效果 */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
  transform: translateY(10rpx);
}

/* 骨架屏样式 */
.skeleton-icons {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 200rpx;
  background: linear-gradient(90deg, #f0f0f0 25%, #f8f8f8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 12rpx;
  display: flex;
  justify-content: space-between;
  padding: 20rpx 0;
  z-index: 1;
}

/* 金刚位图标骨架屏 */
.skeleton-icons::before {
  content: "";
  position: absolute;
  top: 20rpx;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-around;
}

.skeleton-icons::after {
  content: "";
  position: absolute;
  bottom: 20rpx;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-around;
}

.skeleton-sgztmk {
  width: 100%;
  height: 400rpx;
  background: linear-gradient(90deg, #f0f0f0 25%, #f8f8f8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 12rpx;
  margin-bottom: 20rpx;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

.empty-placeholder {
	width: 100%;
	height: 200rpx;
	background-color: rgba(245, 245, 245, 0.5);
	border-radius: 12rpx;
}

:deep(.height-predict-container) {
	background: linear-gradient(135deg, #f5f5f5 0%, #e8f4ff 100%);
	border-radius: 24rpx;
	padding: 30rpx;
	margin: 0;
	box-shadow: 0 8rpx 24rpx rgba(8, 99, 204, 0.1);
	position: relative;
	z-index: 1;
}

.bottom-tip {
	text-align: center;
	padding: 40rpx 0;
	margin-bottom: 40rpx;
	
	text {
	  font-size: 28rpx;
	  color: #999999;
	}
}
</style>
