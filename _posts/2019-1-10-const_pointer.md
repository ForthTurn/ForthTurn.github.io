---
layout: post
title:  "C语言中const的用法详解"
date:   2019-1-10 12:55:20
categories: C语言
tags: const 指针 C语言
excerpt: 关于在C语言中const与指针结合的用法。
mathjax: true
---

const是c语言中的一个常用关键字，用来声明一个变量是不可改变的常量。  
这里我们看一下const的几种用法：  

# 一、使用指针改变常量的值： #

```c
    const int a = 233;
```

这样我们就声明了一个常量名为`a`的，值为233的整型常量。  
如果我们想要改变a的值，例如：

```c
    a = 2233;
```

那么编译器会报错，并且不予通过编译。  
那么如果真的需要改变一个const声明的常量的值，我们可以通过指针来实现：  

```c
    int *p;
    p = &a;
    *p = 2233;
```

这样编译器会给出警告，并且作废const声明，改变a的值。  

# 二、有const定义的指针 #  

## 1.int const * ##

这样定义出来的指针表示一个指向整型常量的指针，但指针本身并不是常量，  
也就是说，指针内部存储的地址不是固定的，而是可变的。  
可以用这个指针指向其他的变量。  
但是不能使用`间接寻址符 *`来改变其指向的变量的值。  

## 2.const int * ##

等价于`int const *`

## 3.int *const ##

这样定义的是一个常量指针指向变量对象。
指向的对象是可以改变的，而指针的地址是不能改变的。

## 4.const int* const ##

这个很好理解，两个const都存在，那显然是常量指针指向常量对象。  
指针的地址不能改变，指针所指向的对象也不能改变。  
等价于`int const*const`

# 三、总结 #

英文中可以这么说：

`int const*` is pointer to const int  
`int *const` is const pointer to int  
`int const* const` is const pointer to const int  

Using this rule, even complex declarations can be decoded like,

`int ** const` is a const pointer to pointer to an int.  
`int * const *` is a pointer to const pointer to an int.  
`int const **` is a pointer to a pointer to a const int.  
`int * const * const` is a const pointer to a const pointer to an int.  
`int const * const * const` is a const pointer to a const poiner to a const int.

总之，对此类定义可以这么解读：  
相邻的const和type可以互换位置，然后从后向前读。可以解决所有该类问题。
