<template>
  <button 
    class="zhuige-button" 
    :class="[
      type === 'primary' ? 'zhuige-button-primary' : 'zhuige-button-secondary',
      size === 'small' ? 'small' : size === 'large' ? 'large' : '',
      block ? 'block' : '',
      round ? 'round' : '',
      disabled ? 'disabled' : ''
    ]"
    :disabled="disabled"
    @click="handleClick"
  >
    <slot></slot>
  </button>
</template>

<script>
export default {
  name: 'ZhuigeButton',
  props: {
    type: {
      type: String,
      default: 'primary'
    },
    size: {
      type: String,
      default: 'medium'
    },
    block: {
      type: Boolean,
      default: false
    },
    round: {
      type: Boolean,
      default: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    handleClick(e) {
      this.$emit('click', e);
    }
  }
}
</script>

<style lang="scss">
@import '@/styles/theme.scss';
@import '@/styles/mixins.scss';

.zhuige-button {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: $spacing-sm $spacing-lg;
  font-size: $font-md;
  font-weight: $weight-medium;
  border: none;
  outline: none;
  transition: all $transition-fast;
  overflow: hidden;
  
  &::after {
    border: none;
  }
  
  &.zhuige-button-primary {
    @include primary-button;
  }
  
  &.zhuige-button-secondary {
    @include secondary-button;
  }
  
  &.small {
    padding: $spacing-xs $spacing-md;
    font-size: $font-sm;
  }
  
  &.large {
    padding: $spacing-md $spacing-xl;
    font-size: $font-lg;
  }
  
  &.block {
    display: flex;
    width: 100%;
  }
  
  &.round {
    border-radius: $radius-full;
  }
  
  &.disabled {
    opacity: 0.5;
    pointer-events: none;
  }
  
  // 添加波纹效果
  &:active::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: inherit;
    transform: translate(-50%, -50%) scale(0);
    animation: ripple $transition-fast forwards;
  }
}

@keyframes ripple {
  to {
    transform: translate(-50%, -50%) scale(2);
    opacity: 0;
  }
}
</style> 