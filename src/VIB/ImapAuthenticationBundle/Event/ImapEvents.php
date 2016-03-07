<?php

namespace VIB\ImapAuthenticationBundle\Event;

final class ImapEvents
{
    const PRE_BIND = 'vib_imap.security.authentication.pre_bind';
    const POST_BIND = 'vib_imap.security.authentication.post_bind';
}
