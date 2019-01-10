#include <stdio.h>

int main(void)
{
    int a = 233;
    const int const* p = &a;
    p = NULL;
    printf("%d\n",*p);
    return 0;
}