<template>
  <view class="shop-container">
    <!-- 顶部分类横滑 -->
    <scroll-view class="category-scroll" scroll-x="true" show-scrollbar="false">
      <view class="category-list">
        <view 
          v-for="(item, index) in categories" 
          :key="index" 
          class="category-item" 
          :class="{ active: currentCategory === item.id }"
          @click="switchCategory(item.id)"
        >
          <text>{{ item.name }}</text>
        </view>
      </view>
    </scroll-view>
    
    <!-- 金刚位模块 -->
    <view class="kingkong-module" v-if="subCategories.length > 0">
      <view class="kingkong-list">
        <view 
          v-for="(item, index) in subCategories" 
          :key="index" 
          class="kingkong-item"
          @click="navigateToSubCategory(item.id)"
        >
          <image class="kingkong-icon" :src="item.icon" mode="aspectFill"></image>
          <text class="kingkong-name">{{ item.name }}</text>
        </view>
      </view>
    </view>
    
    <!-- 推荐配置模块 -->
    <view class="recommend-section" v-if="recommendProducts1.length > 0 || recommendProducts2.length > 0">
      <view class="recommend-row">
        <!-- 推荐配置模块1 -->
        <view class="recommend-module" v-if="recommendProducts1.length > 0">
          <view class="recommend-content">
            <view class="recommend-text">
              <text class="recommend-title">{{ recommendTitle1 }}</text>
              <text class="recommend-desc">{{ recommendDesc1 }}</text>
            </view>
            <view class="recommend-images">
              <view class="recommend-product-row">
                <view class="recommend-product" v-for="(product, index) in recommendProducts1.slice(0, 2)" :key="index" @click="navigateToDetail(product)">
                  <image class="recommend-product-image" :src="product.slides && product.slides.length > 0 ? product.slides[0] : (product.image || '/static/images/default-product.png')" mode="aspectFill"></image>
                </view>
              </view>
            </view>
          </view>
        </view>
        
        <!-- 推荐配置模块2 -->
        <view class="recommend-module" v-if="recommendProducts2.length > 0">
          <view class="recommend-content">
            <view class="recommend-text">
              <text class="recommend-title">{{ recommendTitle2 }}</text>
              <text class="recommend-desc">{{ recommendDesc2 }}</text>
            </view>
            <view class="recommend-images">
              <view class="recommend-product-row">
                <view class="recommend-product" v-for="(product, index) in recommendProducts2.slice(0, 2)" :key="index" @click="navigateToDetail(product)">
                  <image class="recommend-product-image" :src="product.slides && product.slides.length > 0 ? product.slides[0] : (product.image || '/static/images/default-product.png')" mode="aspectFill"></image>
                </view>
              </view>
            </view>
          </view>
        </view>
      </view>
    </view>
    
    <!-- 商品列表 -->
    <view class="product-list">
      <view 
        v-for="(item, index) in products" 
        :key="index" 
        class="product-item"
        @click="navigateToDetail(item)"
      >
        <image class="product-image" :src="item.slides && item.slides.length > 0 ? item.slides[0] : (item.image || '/static/images/default-product.png')" mode="aspectFill"></image>
        <view class="product-info">
          <text class="product-title">{{ item.title }}</text>
          <view class="product-bottom">
            <view class="price-sales">
              <text class="product-price">¥{{ item.price }}</text>
              <text class="product-sales">已售{{ item.sales || 0 }}件</text>
            </view>
          </view>
        </view>
      </view>
    </view>
    
    <!-- 无数据提示 -->
    <zhuige-nodata v-if="products.length === 0"></zhuige-nodata>
    
    <!-- 加载更多 -->
    <view class="loading-more" v-if="isLoading">
      <text>加载中...</text>
    </view>
  </view>
</template>

<script>
import Auth from '@/utils/auth.js';
import Util from '@/utils/util.js';
import ZhuigeNodata from '@/components/zhuige-nodata.vue';
import Api from '@/utils/api.js';

export default {
  components: {
    ZhuigeNodata
  },
  
  data() {
    return {
      categories: [],
      subCategories: [],
      recommendProducts1: [],
      recommendProducts2: [],
      recommendTitle1: '热门推荐',
      recommendTitle2: '新品上市',
      recommendDesc1: '',
      recommendDesc2: '',
      products: [],
      currentCategory: 0,
      initialCategoryId: 0,
      page: 1,
      isLoading: false,
      hasMore: true,
      apiBaseUrl: 'https://x.erquhealth.com/wp-json/zhuige-xcx/v1'
    }
  },
  
  onLoad() {
    console.log('商品分发频道页面加载');
    
    // 检查网络状态
    this.checkNetworkStatus(() => {
      this.testApi();
      this.loadCategories();
    });
  },
  
  onReachBottom() {
    if (this.hasMore && !this.isLoading) {
      this.loadMoreProducts();
    }
  },
  
  onPullDownRefresh() {
    this.refreshData();
  },
  
  methods: {
    // 检查API URL有效性
    checkApiUrlValidity() {
      // 使用相对路径，不依赖于Api.ZHUIGE_API_URL
      // 直接使用WordPress REST API的相对路径
      return true;
    },
    
    // 获取一级分类页面路径
    getCategoryPagePath(categoryId) {
      // 返回首页路径，并带上分类ID参数
      return '/pages/sgtool/shop/index?category_id=' + categoryId;
    },
    
    // 获取二级分类页面路径
    getSubCategoryPagePath(subCategoryId) {
      // 返回二级分类详情页路径
      return '/pages/sgtool/shop/subcategory?id=' + subCategoryId;
    },
    
    // 检查网络状态
    checkNetworkStatus(callback) {
      uni.getNetworkType({
        success: (res) => {
          console.log('当前网络类型:', res.networkType);
          if (res.networkType === 'none') {
            console.error('网络不可用');
            uni.showToast({
              title: '网络不可用，请检查网络设置',
              icon: 'none',
              duration: 3000
            });
          } else {
            if (typeof callback === 'function') {
              callback();
            }
          }
        }
      });
    },
    
    // 测试API接口
    testApi() {
      console.log('开始测试API接口');
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/test';
      console.log('请求URL:', apiUrl);
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        success: (res) => {
          console.log('测试API请求成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            console.log('API系统正常工作:', res.data.data);
          } else {
            console.error('测试API请求返回错误:', res.data);
            uni.showToast({
              title: '测试API返回错误: ' + (res.data.msg || '未知错误'),
              icon: 'none',
              duration: 3000
            });
          }
        },
        fail: (err) => {
          console.error('测试API请求失败:', err);
          // 显示详细的错误信息
          let errMsg = '';
          if (err.errMsg) {
            errMsg = err.errMsg;
          } else if (typeof err === 'object') {
            errMsg = JSON.stringify(err);
          } else {
            errMsg = String(err);
          }
          
          uni.showToast({
            title: '网络请求失败: ' + errMsg,
            icon: 'none',
            duration: 3000
          });
        }
      });
    },
    
    // 加载分类
    loadCategories() {
      this.isLoading = true;
      console.log('开始加载分类数据');
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/categories';
      console.log('请求URL:', apiUrl);
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        header: {
          'content-type': 'application/json'
        },
        success: (res) => {
          console.log('分类数据请求成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            // 分类已经按照 sort_order 排序，直接使用
            this.categories = res.data.data;
            console.log('获取到分类数据:', this.categories);
            
            // 如果有初始分类ID，则使用它
            if (this.initialCategoryId && this.categories.find(cat => cat.id === this.initialCategoryId)) {
              this.currentCategory = this.initialCategoryId;
              console.log('使用初始分类ID:', this.currentCategory);
            } else {
              // 默认选择排序为1的分类，如果没有则选择第一个
              let defaultCategory = this.categories.find(cat => cat.sort_order === 1);
              if (!defaultCategory && this.categories.length > 0) {
                defaultCategory = this.categories[0];
              }
              
              if (defaultCategory) {
                this.currentCategory = defaultCategory.id;
                console.log('设置当前分类ID:', this.currentCategory);
              }
            }
            
            if (this.currentCategory) {
              this.loadSubCategories();
              this.loadRecommendProducts();
              this.loadProducts();
            } else {
              console.log('没有获取到分类数据');
            }
          } else {
            console.error('分类数据请求返回错误:', res.data);
            uni.showToast({
              title: res.data.msg || '加载分类失败',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          console.error('分类数据请求失败:', err);
          uni.showToast({
            title: '网络错误: ' + JSON.stringify(err),
            icon: 'none'
          });
        },
        complete: () => {
          this.isLoading = false;
          console.log('分类数据请求完成');
        }
      });
    },
    
    // 加载二级分类
    loadSubCategories() {
      console.log('开始加载二级分类数据');
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/subcategories';
      console.log('请求URL:', apiUrl);
      console.log('请求参数:', { parent_id: this.currentCategory });
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        data: {
          parent_id: this.currentCategory
        },
        success: (res) => {
          console.log('二级分类数据请求成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            this.subCategories = res.data.data;
            console.log('获取到二级分类数据:', this.subCategories);
          } else {
            console.error('二级分类数据请求返回错误:', res.data);
          }
        },
        fail: (err) => {
          console.error('二级分类数据请求失败:', err);
        }
      });
    },
    
    // 加载推荐商品
    loadRecommendProducts() {
      console.log('开始加载推荐商品数据');
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/recommend';
      console.log('请求URL:', apiUrl);
      console.log('请求参数:', { category_id: this.currentCategory });
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        data: {
          category_id: this.currentCategory
        },
        success: (res) => {
          console.log('推荐商品数据请求成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            const data = res.data.data;
            if (data.recommend1) {
              this.recommendProducts1 = data.recommend1.products || [];
              this.recommendTitle1 = data.recommend1.title || '热门推荐';
              this.recommendDesc1 = data.recommend1.desc || '';
              console.log('获取到推荐模块1数据:', this.recommendProducts1);
            } else {
              this.recommendProducts1 = [];
              this.recommendTitle1 = '热门推荐';
              this.recommendDesc1 = '';
            }
            
            if (data.recommend2) {
              this.recommendProducts2 = data.recommend2.products || [];
              this.recommendTitle2 = data.recommend2.title || '新品上市';
              this.recommendDesc2 = data.recommend2.desc || '';
              console.log('获取到推荐模块2数据:', this.recommendProducts2);
            } else {
              this.recommendProducts2 = [];
              this.recommendTitle2 = '新品上市';
              this.recommendDesc2 = '';
            }
          } else {
            console.error('推荐商品数据请求返回错误:', res.data);
          }
        },
        fail: (err) => {
          console.error('推荐商品数据请求失败:', err);
        }
      });
    },
    
    // 加载商品
    loadProducts() {
      this.isLoading = true;
      this.page = 1;
      this.products = [];
      
      console.log('开始加载商品数据');
      // 使用apiBaseUrl替代Api.HOST
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/products';
      console.log('请求URL:', apiUrl);
      console.log('请求参数:', {
        category_id: this.currentCategory,
        page: this.page,
        per_page: 10
      });
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        data: {
          category_id: this.currentCategory,
          page: this.page,
          per_page: 10
        },
        success: (res) => {
          console.log('商品数据请求成功:', res);
          if (res.statusCode === 200 && res.data.code === 200) {
            this.products = res.data.data.list;
            this.hasMore = res.data.data.more;
            console.log('获取到商品数据:', this.products);
          } else {
            console.error('商品数据请求返回错误:', res.data);
            uni.showToast({
              title: res.data.msg || '加载商品失败',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          console.error('商品数据请求失败:', err);
          uni.showToast({
            title: '网络错误',
            icon: 'none'
          });
        },
        complete: () => {
          this.isLoading = false;
          uni.stopPullDownRefresh();
          console.log('商品数据请求完成');
        }
      });
    },
    
    // 加载更多商品
    loadMoreProducts() {
      if (!this.hasMore || this.isLoading) {
        return;
      }
      
      this.isLoading = true;
      this.page++;
      
      // 使用apiBaseUrl
      const apiUrl = this.apiBaseUrl + '/sgtool/shop/products';
      
      uni.request({
        url: apiUrl,
        method: 'GET',
        data: {
          category_id: this.currentCategory,
          page: this.page,
          per_page: 10
        },
        success: (res) => {
          if (res.statusCode === 200 && res.data.code === 200) {
            this.products = [...this.products, ...res.data.data.list];
            this.hasMore = res.data.data.more;
          } else {
            this.page--;
            uni.showToast({
              title: res.data.msg || '加载更多失败',
              icon: 'none'
            });
          }
        },
        fail: (err) => {
          this.page--;
          uni.showToast({
            title: '网络错误',
            icon: 'none'
          });
        },
        complete: () => {
          this.isLoading = false;
        }
      });
    },
    
    // 切换分类
    switchCategory(categoryId) {
      if (this.currentCategory === categoryId) {
        return;
      }
      
      this.currentCategory = categoryId;
      this.loadSubCategories();
      this.loadRecommendProducts();
      this.loadProducts();
    },
    
    // 刷新数据
    refreshData() {
      this.loadCategories();
    },
    
    // 跳转到二级分类
    navigateToSubCategory(subCategoryId) {
      uni.navigateTo({
        url: '/pages/sgtool/shop/subcategory?id=' + subCategoryId
      });
    },
    
    // 跳转到商品详情
    navigateToDetail(product) {
      if (product.app_id && product.path) {
        // 跳转到其他小程序
        uni.navigateToMiniProgram({
          appId: product.app_id,
          path: product.path,
          success: function() {
            console.log('跳转成功');
          },
          fail: function(err) {
            console.error('跳转失败', err);
            uni.showToast({
              title: '跳转失败',
              icon: 'none'
            });
          }
        });
      }
    }
  }
}
</script>

<style scoped>
.shop-container {
  min-height: 100vh;
  background-color: #f8f8f8;
  padding-bottom: 30rpx;
}

.category-scroll {
  background-color: #ffffff;
  height: 90rpx;
  white-space: nowrap;
  position: sticky;
  top: 0;
  z-index: 10;
  box-shadow: 0 2rpx 6rpx rgba(0, 0, 0, 0.05);
}

.category-list {
  display: flex;
  padding: 0 20rpx;
}

.category-item {
  display: inline-block;
  padding: 0 30rpx;
  height: 90rpx;
  line-height: 90rpx;
  font-size: 32rpx;
  color: #333;
  position: relative;
}

.category-item.active {
  color: #ff5722;
  font-weight: bold;
}

.category-item.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 60rpx;
  height: 6rpx;
  background-color: #ff5722;
  border-radius: 3rpx;
}

.kingkong-module {
  background-color: #ffffff;
  padding: 10rpx 20rpx;
  margin: 0 0 4rpx 0;
  border-radius: 12rpx;
  box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.03);
}

.kingkong-list {
  display: flex;
  flex-wrap: wrap;
}

.kingkong-item {
  width: 20%;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx 0;
}

.kingkong-icon {
  width: 90rpx;
  height: 90rpx;
  border-radius: 50%;
  box-shadow: 0 4rpx 8rpx rgba(0, 0, 0, 0.1);
}

.kingkong-name {
  font-size: 24rpx;
  color: #333;
  margin-top: 16rpx;
  text-align: center;
}

/* 推荐模块样式 - 按照效果图重新设计 */
.recommend-section {
  padding: 0;
  margin: 0; /* 上下外间距为0 */
  background-color: #ffffff;
}

.recommend-row {
  display: flex;
  justify-content: space-between;
  position: relative;
  background-color: #ffffff;
  padding: 10rpx 20rpx;
}

.recommend-row::after {
  content: '';
  position: absolute;
  top: 15%;
  bottom: 15%;
  left: 50%;
  width: 1rpx;
  background-color: #eee;
  transform: translateX(-50%);
}

.recommend-module {
  width: 48%;
  background-color: #ffffff;
  border-radius: 12rpx;
  overflow: hidden;
  padding: 10rpx;
  display: flex;
  justify-content: center;
}

.recommend-content {
  display: flex;
  height: 100%;
  justify-content: center;
  align-items: center;
  width: 90%;
}

.recommend-text {
  flex: 1;
  margin-right: 15rpx;
}

.recommend-title {
  font-size: 28rpx;
  color: #333;
  display: block;
  margin-bottom: 8rpx;
}

.recommend-desc {
  font-size: 24rpx;
  color: #999;
  display: block;
}

.recommend-images {
  width: 150rpx;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.recommend-product-row {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  width: 100%;
}

.recommend-product {
  width: 70rpx;
  height: 70rpx;
  overflow: hidden;
  border-radius: 6rpx;
  margin-right: 5rpx;
}

.recommend-product:last-child {
  margin-right: 0;
}

.recommend-product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* 商品列表样式 - 优化布局 */
.product-list {
  display: flex;
  flex-wrap: wrap;
  padding: 4rpx;
  margin: 0;
  background-color: #f8f8f8;
}

.product-item {
  width: calc(50% - 10rpx);
  margin: 5rpx;
  background-color: #ffffff;
  overflow: hidden;
  border-radius: 8rpx;
  box-shadow: 0 2rpx 8rpx rgba(0, 0, 0, 0.05);
}

.product-image {
  width: 100%;
  height: 345rpx;
  object-fit: cover;
}

.product-info {
  padding: 10rpx 16rpx 16rpx;
}

.product-title {
  font-size: 28rpx;
  color: #333;
  line-height: 1.3;
  height: 36rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  padding-right: 28rpx;
  margin-bottom: 0; /* 去掉商品标题和价格之间的间距 */
}

.product-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price-sales {
  display: flex;
  align-items: center;
}

.product-price {
  font-size: 32rpx;
  color: #ff5722;
  font-weight: bold;
  margin-right: 10rpx;
}

.product-sales {
  font-size: 22rpx;
  color: #999;
}

.loading-more {
  text-align: center;
  padding: 30rpx 0;
  color: #999;
  font-size: 24rpx;
}
</style> 